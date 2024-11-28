<?php

declare(strict_types=1);

namespace XbNz\Ip\Providers;

use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use XbNz\Ip\Contracts\RapidParserInterface;
use XbNz\Ip\Subscribers\IpAddressSubscriber;
use XbNz\Masscan\MasscanRapidParser;
use XbNz\Shared\Actions\ResolveListenersAction;

final class IpServiceProvider extends ServiceProvider
{
    /**
     * @var array<int, class-string>
     */
    private array $subscribers = [
        IpAddressSubscriber::class,
    ];

    public function register(): void
    {
        $this->app->bind(RapidParserInterface::class, fn (Application $app) => $app->make(MasscanRapidParser::class));
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
