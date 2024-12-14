<?php

declare(strict_types=1);

namespace XbNz\Preferences\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use XbNz\Preferences\Subscribers\FpingSubscriber;
use XbNz\Shared\Actions\ResolveListenersAction;

final class PreferencesServiceProvider extends ServiceProvider
{
    /**
     * @var array<int, class-string>
     */
    private array $subscribers = [
        FpingSubscriber::class,
    ];

    public function register(): void {}

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
