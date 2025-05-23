<?php

declare(strict_types=1);

namespace XbNz\Masscan\Providers;

use Illuminate\Support\ServiceProvider;
use XbNz\Masscan\Contracts\MasscanIcmpScannerInterface;
use XbNz\Masscan\MasscanIcmpScanner;

final class MasscanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MasscanIcmpScannerInterface::class, MasscanIcmpScanner::class);

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'masscan');
    }

    public function boot(): void {}
}
