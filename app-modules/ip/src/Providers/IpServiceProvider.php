<?php

declare(strict_types=1);

namespace XbNz\Ip\Providers;

use Illuminate\Support\ServiceProvider;
use XbNz\Ip\Actions\ImportIpAddressesAction;
use XbNz\Ip\Contracts\ImportIpAddressesContract;

final class IpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ImportIpAddressesContract::class,
            ImportIpAddressesAction::class
        );
    }

    public function boot(): void {}
}
