<?php

declare(strict_types=1);

namespace XbNz\Fping\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use XbNz\Fping\Contracts\FpingInterface;
use XbNz\Fping\Fping;
use XbNz\Fping\Subscribers\FpingSubscriber;
use XbNz\Shared\Actions\ResolveListenersAction;

final class FpingServiceProvider extends ServiceProvider
{
    /**
     * @var array<int, class-string>
     */
    private array $subscribers = [
        FpingSubscriber::class
    ];

    public function register(): void
    {
        $this->app->bind(FpingInterface::class, Fping::class);

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'fping');
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
