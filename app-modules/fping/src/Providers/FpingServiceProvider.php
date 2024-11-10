<?php

declare(strict_types=1);

namespace XbNz\Fping\Providers;

use Illuminate\Support\ServiceProvider;

final class FpingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'fping');
    }
}
