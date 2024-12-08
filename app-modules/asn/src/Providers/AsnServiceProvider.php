<?php

declare(strict_types=1);

namespace XbNz\Asn\Providers;

use Illuminate\Support\ServiceProvider;
use XbNz\RouteviewsIntegration\RouteViewsAsnToRange;
use XbNz\RouteviewsIntegration\RouteViewsIpToAsn;

final class AsnServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->tag([RouteViewsIpToAsn::class], 'ip-to-asn');
        $this->app->tag([RouteViewsAsnToRange::class], 'asn-to-range');
    }

    public function boot(): void {}
}
