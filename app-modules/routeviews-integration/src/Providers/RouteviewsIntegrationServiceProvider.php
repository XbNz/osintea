<?php

declare(strict_types=1);

namespace XbNz\RouteviewsIntegration\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MaxMind\Db\Reader;
use XbNz\RouteviewsIntegration\RouteViewsIpToAsn;

final class RouteviewsIntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'routeviews-integration');

        $this->app->singleton(RouteViewsIpToAsn::class, function (Application $application) {
            return new RouteViewsIpToAsn(
                new Reader($application->make(Repository::class)->get('routeviews-integration.asn_mmdb.ipv4')),
                new Reader($application->make(Repository::class)->get('routeviews-integration.asn_mmdb.ipv6'))
            );
        });
    }

    public function boot(): void {}
}
