<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Updaters;

use GuzzleHttp\RequestOptions;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Lottery;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use XbNz\Shared\Contracts\UpdaterInterface;
use XbNz\Shared\Enums\UpdatableDatabase;
use XbNz\Shared\Events\UpdateProgressReportEvent;

final class Geolite2CityIpv6MmdbUpdater implements UpdaterInterface
{
    public function __construct(
        private readonly Dispatcher $dispatcher,
        private readonly Factory $laravelHttp,
        private readonly Filesystem $filesystem,
        private readonly Repository $config,
    ) {}

    public function update(): void
    {
        $temporaryDir = TemporaryDirectory::make()
            ->force()
            ->create();

        $temporaryIpv6Mmdb = $temporaryDir->path('geolite2-city-ipv6.mmdb');

        touch($temporaryIpv6Mmdb);

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
                        UpdatableDatabase::MaxmindGeolite2CityMmdbIpv6,
                        $totalBytes,
                        $downloadedBytes,
                    ),
                );
            },
        ];

        $response = $this->laravelHttp
            ->withOptions($options)
            ->get('https://github.com/sapics/ip-location-db/raw/refs/heads/main/geolite2-city-mmdb/geolite2-city-ipv6.mmdb')
            ->throw();

        while ($response->getBody()->eof() === false) {
            file_put_contents($temporaryIpv6Mmdb, $response->getBody()->read(4096), FILE_APPEND);
        }

        $this->filesystem->move($temporaryIpv6Mmdb, $this->config->get('maxmind-integration.geolite2_city_mmdb.ipv6'));
    }

    public function supports(UpdatableDatabase $database): bool
    {
        return $database === UpdatableDatabase::MaxmindGeolite2CityMmdbIpv6;
    }
}