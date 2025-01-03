<?php

declare(strict_types=1);

namespace XbNz\RouteviewsIntegration\Updaters;

use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
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

        $temporaryIpv4Csv = $temporaryDir->path('asn-ipv4.csv');
        $temporaryIpv6Csv = $temporaryDir->path('asn-ipv6.csv');

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
                            UpdatableDatabase::RouteViewsAsnUnifiedSqlite,
                            $totalBytes,
                            $downloadedBytes,
                        ),
                    );
                },
            ];

            return [
                $pool->withOptions($options)->get('https://github.com/sapics/ip-location-db/raw/refs/heads/main/asn/asn-ipv4.csv'),
                $pool->withOptions($options)->get('https://github.com/sapics/ip-location-db/raw/refs/heads/main/asn/asn-ipv6.csv'),
            ];
        });

        $responses[0]->throw();
        $responses[1]->throw();

        while ($responses[0]->getBody()->eof() === false) {
            file_put_contents($temporaryIpv4Csv, $responses[0]->getBody()->read(4096), FILE_APPEND);
        }

        while ($responses[1]->getBody()->eof() === false) {
            file_put_contents($temporaryIpv6Csv, $responses[1]->getBody()->read(4096), FILE_APPEND);
        }

        $this->database->beginTransaction();

        try {
            $this->database->table('route_views_v4_asns')->truncate();
            $this->database->table('route_views_v6_asns')->truncate();

            $this->filesystem->lines($temporaryIpv4Csv)
                ->chunk(2000)
                ->each(function (LazyCollection $chunk): void {
                    $data = $chunk->map(fn (string $line) => Str::of($line)->explode(','))
                        ->map(fn (Collection $line) => [
                            'start_ip' => $line->get(0),
                            'end_ip' => $line->get(1),
                            'asn' => $line->get(2),
                            'organization' => $line->get(3),
                        ]);

                    $this->database->table('route_views_v4_asns')->insertOrIgnore($data->toArray());
                });

            $this->filesystem->lines($temporaryIpv6Csv)
                ->chunk(2000)
                ->each(function (LazyCollection $chunk): void {
                    $data = $chunk->map(fn (string $line) => Str::of($line)->explode(','))
                        ->map(fn (Collection $line) => [
                            'start_ip' => $line->get(0),
                            'end_ip' => $line->get(1),
                            'asn' => $line->get(2),
                            'organization' => $line->get(3),
                        ]);

                    $this->database->table('route_views_v6_asns')->insertOrIgnore($data->toArray());
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
        return $database === UpdatableDatabase::RouteViewsAsnUnifiedSqlite;
    }
}
