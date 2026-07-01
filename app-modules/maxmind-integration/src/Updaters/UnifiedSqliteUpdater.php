<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Updaters;

use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Lottery;
use Illuminate\Support\Str;
use Psr\Http\Message\StreamInterface;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Throwable;
use UnexpectedValueException;
use XbNz\Shared\Contracts\UpdaterInterface;
use XbNz\Shared\Enums\UpdatableDatabase;
use XbNz\Shared\Events\UpdateProgressReportEvent;

final class UnifiedSqliteUpdater implements UpdaterInterface
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly Factory $laravelHttp,
        private readonly DatabaseManager $database,
        private readonly Filesystem $filesystem,
    ) {}

    public function update(): void
    {
        $temporaryDir = TemporaryDirectory::make()
            ->force()
            ->create();

        $temporaryIpv4CsvGz = $temporaryDir->path('geo-ipv4.csv.gz');
        $temporaryIpv6CsvGz = $temporaryDir->path('geo-ipv6.csv.gz');
        $temporaryIpv4Csv = $temporaryDir->path('geo-ipv4.csv');
        $temporaryIpv6Csv = $temporaryDir->path('geo-ipv6.csv');

        touch($temporaryIpv4CsvGz);
        touch($temporaryIpv6CsvGz);
        touch($temporaryIpv4Csv);
        touch($temporaryIpv6Csv);

        $responses = $this->laravelHttp->pool(function (Pool $pool) {
            $options = [
                RequestOptions::PROGRESS => function (int $totalBytes, int $downloadedBytes): void {
                    if ($totalBytes === 0) {
                        return;
                    }

                    $hit = Lottery::odds(1, 250)->choose();

                    if ($hit === false && $downloadedBytes / $totalBytes !== 1) {
                        return;
                    }

                    $this->dispatcher->dispatch(
                        new UpdateProgressReportEvent(
                            UpdatableDatabase::MaxmindGeoLite2CityUnifiedSqlite,
                            $totalBytes,
                            $downloadedBytes,
                        ),
                    );
                },
            ];

            return [
                $pool->withOptions($options)->get('https://github.com/sapics/ip-location-db/raw/refs/heads/main/geolite2-city/geolite2-city-ipv4.csv.gz'),
                $pool->withOptions($options)->get('https://github.com/sapics/ip-location-db/raw/refs/heads/main/geolite2-city/geolite2-city-ipv6.csv.gz'),
            ];
        });

        $ipv4Body = $this->bodyFromResponse($responses[0]);
        $ipv6Body = $this->bodyFromResponse($responses[1]);

        while ($ipv4Body->eof() === false) {
            file_put_contents($temporaryIpv4CsvGz, $ipv4Body->read(4096), FILE_APPEND);
        }

        while ($ipv6Body->eof() === false) {
            file_put_contents($temporaryIpv6CsvGz, $ipv6Body->read(4096), FILE_APPEND);
        }

        $ipv4GzResource = \Safe\gzopen($temporaryIpv4CsvGz, 'rb');
        $ipv6GzResource = \Safe\gzopen($temporaryIpv6CsvGz, 'rb');

        $ipv4CsvResource = \Safe\fopen($temporaryIpv4Csv, 'wb');
        $ipv6CsvResource = \Safe\fopen($temporaryIpv6Csv, 'wb');

        while (gzeof($ipv4GzResource) === false) {
            $chunk = \Safe\gzread($ipv4GzResource, 4096);
            fwrite($ipv4CsvResource, $chunk);
        }

        while (gzeof($ipv6GzResource) === false) {
            $chunk = \Safe\gzread($ipv6GzResource, 4096);
            fwrite($ipv6CsvResource, $chunk);
        }

        \Safe\gzclose($ipv4GzResource);
        \Safe\gzclose($ipv6GzResource);
        \Safe\fclose($ipv4CsvResource);
        \Safe\fclose($ipv6CsvResource);

        $this->database->beginTransaction();

        try {
            $this->database->table('maxmind_v4_geolocations')->truncate();
            $this->database->table('maxmind_v6_geolocations')->truncate();

            $this->filesystem->lines($temporaryIpv4Csv)
                ->map(fn (string $line) => Str::of($line)->explode(','))
                ->filter(fn (Collection $line) => is_numeric($line[7] ?? null) && is_numeric($line[8] ?? null))
                ->map(fn (Collection $line) => [
                    'start_ip' => $line[0],
                    'end_ip' => $line[1],
                    'coordinates' => "POINT({$line[7]} {$line[8]})",
                ])
                ->chunk(2000)
                ->each(function (LazyCollection $chunk): void {
                    $this->insertGeolocations('maxmind_v4_geolocations', $chunk);
                });

            $this->filesystem->lines($temporaryIpv6Csv)
                ->map(fn (string $line) => Str::of($line)->explode(','))
                ->filter(fn (Collection $line) => is_numeric($line[7] ?? null) && is_numeric($line[8] ?? null))
                ->map(fn (Collection $line) => [
                    'start_ip' => $line[0],
                    'end_ip' => $line[1],
                    'coordinates' => "POINT({$line[7]} {$line[8]})",
                ])
                ->chunk(2000)
                ->each(function (LazyCollection $chunk): void {
                    $this->insertGeolocations('maxmind_v6_geolocations', $chunk);
                });

        } catch (Throwable $e) {
            $this->database->rollBack();
            throw $e;
        }

        $this->database->commit();

        $temporaryDir->delete();
    }

    public function supports(UpdatableDatabase $database): bool
    {
        return $database === UpdatableDatabase::MaxmindGeoLite2CityUnifiedSqlite;
    }

    /**
     * @param  LazyCollection<int|string, array{start_ip: string|null, end_ip: string|null, coordinates: string}>  $chunk
     */
    private function insertGeolocations(string $table, LazyCollection $chunk): void
    {
        $rows = $chunk->values();

        if ($rows->isEmpty()) {
            return;
        }

        $placeholders = $rows
            ->map(fn (): string => '(?, ?, ST_GeomFromText(?, 4326))')
            ->join(', ');

        $bindings = $rows
            ->flatMap(fn (array $row): array => [$row['start_ip'], $row['end_ip'], $row['coordinates']])
            ->all();

        $this->database->insert("INSERT OR IGNORE INTO {$table} (start_ip, end_ip, coordinates) VALUES {$placeholders}", $bindings);
    }

    private function bodyFromResponse(mixed $response): StreamInterface
    {
        if ($response instanceof Throwable) {
            throw $response;
        }

        if ( ! $response instanceof Response) {
            throw new UnexpectedValueException('Expected an HTTP response from the download pool');
        }

        return $response->throw()->toPsrResponse()->getBody();
    }
}
