<?php

declare(strict_types=1);

namespace XbNz\Masscan\Providers;

use Illuminate\Support\ServiceProvider;

final class MasscanServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'masscan');
    }

    public function boot(): void {}
}
