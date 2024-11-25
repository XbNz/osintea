<?php

declare(strict_types=1);

namespace XbNz\Ip\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use XbNz\Ip\Contracts\RapidParserInterface;
use XbNz\Masscan\MasscanRapidParser;

final class IpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RapidParserInterface::class, fn (Application $app) => $app->make(MasscanRapidParser::class));
    }

    public function boot(): void {}
}
