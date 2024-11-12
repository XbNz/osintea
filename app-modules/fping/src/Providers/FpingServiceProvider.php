<?php

declare(strict_types=1);

namespace XbNz\Fping\Providers;

use Illuminate\Support\ServiceProvider;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Fping\Fping;

final class FpingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FpingInterface::class, Fping::class);

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'fping');
    }
}
