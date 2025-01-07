<?php

declare(strict_types=1);

namespace XbNz\Preferences\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use XbNz\MaxmindIntegration\Updaters\Geolite2CityIpv4MmdbUpdater;
use XbNz\MaxmindIntegration\Updaters\Geolite2CityIpv6MmdbUpdater;
use XbNz\MaxmindIntegration\Updaters\UnifiedSqliteUpdater as MaxmindUnifiedSqliteUpdater;
use XbNz\Preferences\Subscribers\FpingSubscriber;
use XbNz\RouteviewsIntegration\Updaters\Ipv4MmdbUpdater;
use XbNz\RouteviewsIntegration\Updaters\Ipv6MmdbUpdater;
use XbNz\RouteviewsIntegration\Updaters\UnifiedSqliteUpdater as RouteviewsUnifiedSqliteUpdater;
use XbNz\Shared\Actions\ResolveListenersAction;

final class PreferencesServiceProvider extends ServiceProvider
{
    /**
     * @var array<int, class-string>
     */
    private array $subscribers = [
        FpingSubscriber::class,
    ];

    public function register(): void
    {
        $this->app->tag([
            RouteviewsUnifiedSqliteUpdater::class,
            MaxmindUnifiedSqliteUpdater::class,
            Ipv4MmdbUpdater::class,
            Ipv6MmdbUpdater::class,
            Geolite2CityIpv4MmdbUpdater::class,
            Geolite2CityIpv6MmdbUpdater::class,
        ], 'database-updaters');
    }

    public function boot(): void
    {
        $eventDispatcher = $this->app->make(Dispatcher::class);

        foreach ($this->subscribers as $subscriber) {
            foreach (
                $this->app->make(ResolveListenersAction::class)->handle($subscriber) as [$event, $listener]
            ) {
                $eventDispatcher->listen($event, $listener);
            }
        }
    }
}
