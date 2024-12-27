<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Updaters;

use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Expression;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Lottery;
use Illuminate\Support\Str;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Throwable;
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

        $responses[0]->throw();
        $responses[1]->throw();

        while ($responses[0]->getBody()->eof() === false) {
            file_put_contents($temporaryIpv4CsvGz, $responses[0]->getBody()->read(4096), FILE_APPEND);
        }

        while ($responses[1]->getBody()->eof() === false) {
            file_put_contents($temporaryIpv6CsvGz, $responses[1]->getBody()->read(4096), FILE_APPEND);
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
                ->map(function (Collection $line) {
                    return [
                        'start_ip' => $line[0],
                        'end_ip' => $line[1],
                        'coordinates' => new Expression("ST_GeomFromText('POINT({$line[7]} {$line[8]})', 4326)"),
                    ];
                })
                ->chunk(2000)
                ->each(function (LazyCollection $chunk): void {
                    $this->database->table('maxmind_v4_geolocations')->insertOrIgnore($chunk->toArray());
                });

            $this->filesystem->lines($temporaryIpv6Csv)
                ->map(fn (string $line) => Str::of($line)->explode(','))
                ->filter(fn (Collection $line) => is_numeric($line[7] ?? null) && is_numeric($line[8] ?? null))
                ->map(function (Collection $line) {
                    return [
                        'start_ip' => $line[0],
                        'end_ip' => $line[1],
                        'coordinates' => new Expression("ST_GeomFromText('POINT({$line[7]} {$line[8]})', 4326)"),
                    ];
                })
                ->chunk(2000)
                ->each(function (LazyCollection $chunk): void {
                    $this->database->table('maxmind_v6_geolocations')->insertOrIgnore($chunk->toArray());
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
}
