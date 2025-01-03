<?php

declare(strict_types=1);

namespace XbNz\MaxmindIntegration\Providers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MaxMind\Db\Reader;
use XbNz\MaxmindIntegration\MaxmindIpToCoordinates;

final class MaxmindIntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'maxmind-integration');

        $this->app->bind(MaxmindIpToCoordinates::class, fn (Application $application) => new MaxmindIpToCoordinates(
            new Reader($application->make(Repository::class)->get('maxmind-integration.geolite2_city_mmdb.ipv4')),
            new Reader($application->make(Repository::class)->get('maxmind-integration.geolite2_city_mmdb.ipv6')),
        ));
    }

    public function boot(): void {}
}
