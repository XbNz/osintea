<?php

declare(strict_types=1);

namespace XbNz\Location\Providers;

use Illuminate\Config\Repository;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use Webmozart\Assert\Assert;
use XbNz\MaxmindIntegration\MaxmindIpToCoordinates;
use XbNz\MaxmindIntegration\MaxmindPolygonToRange;

final class LocationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->tag([MaxmindPolygonToRange::class], 'polygon-to-range');
        $this->app->tag([MaxmindIpToCoordinates::class], 'ip-to-coordinates');

        //        $spatialitePath = \Safe\realpath(__DIR__.'/../../spatialite_libs/darwin_arm64/mod_spatialite.dylib');

        //        $reflector = new ReflectionClass(Connection::class);
        //        $connection = $reflector->newLazyProxy(function (Connection $connection) use ($spatialitePath) {
        //            $connection = $this->app->make(DatabaseManager::class)->connection();
        //
        //            Assert::methodExists($connection->getPdo(), 'loadExtension');
        //
        //            $connection->getPdo()->loadExtension($spatialitePath);
        //
        //            return $connection;
        //        });

        //        $this->app->singleton(DatabaseManager::class, function (Application $app) use ($spatialitePath) {
        //            $connection = $app->make(DatabaseManager::class)->connection();
        //
        //            Assert::methodExists($connection->getPdo(), 'loadExtension');
        //
        //            $connection->getPdo()->loadExtension($spatialitePath);
        //
        //            return $this->app->make(DatabaseManager::class);
        //        });
    }

    public function boot(): void
    {
        if ($this->app->runningUnitTests()) {
            $this->configureSpatialite();
        }

        if ($this->app->make(Repository::class)->get('nativephp-internal.running') === true) {
            $this->configureSpatialite();
        }
    }

    private function configureSpatialite(): void
    {
        $spatialitePath = \Safe\realpath(__DIR__.'/../../spatialite_libs/darwin_arm64/mod_spatialite.dylib');

        $connection = $this->app->make(DatabaseManager::class)->connection();

        Assert::methodExists($connection->getPdo(), 'loadExtension');

        $connection->getPdo()->loadExtension($spatialitePath);
    }
}
