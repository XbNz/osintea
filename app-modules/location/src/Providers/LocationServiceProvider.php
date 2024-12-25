<?php

declare(strict_types=1);

namespace XbNz\Location\Providers;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\ServiceProvider;
use Webmozart\Assert\Assert;

final class LocationServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $spatialitePath = \Safe\realpath(__DIR__.'/../../bin/mod_spatialite.dylib');

        Assert::methodExists($pdo = $this->app->make(DatabaseManager::class)->connection()->getPdo(), 'loadExtension');
        $pdo->loadExtension($spatialitePath);
    }
}
