<?php

declare(strict_types=1);

namespace App\Providers;

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;

final class IconServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory): void {
            $factory->add('fas', [
                'path' => resource_path('svg/solid'),
                'prefix' => 'fas',
            ]);

            $factory->add('fab', [
                'path' => resource_path('svg/brands'),
                'prefix' => 'fab',
            ]);

            $factory->add('fad', [
                'path' => resource_path('svg/duotone'),
                'prefix' => 'fad',
            ]);

            $factory->add('fal', [
                'path' => resource_path('svg/light'),
                'prefix' => 'fal',
            ]);

            $factory->add('far', [
                'path' => resource_path('svg/regular'),
                'prefix' => 'far',
            ]);

            $factory->add('fasl', [
                'path' => resource_path('svg/sharp-light'),
                'prefix' => 'fasl',
            ]);

            $factory->add('fasr', [
                'path' => resource_path('svg/sharp-regular'),
                'prefix' => 'fasr',
            ]);

            $factory->add('fass', [
                'path' => resource_path('svg/sharp-solid'),
                'prefix' => 'fass',
            ]);

            $factory->add('fast', [
                'path' => resource_path('svg/sharp-thin'),
                'prefix' => 'fast',
            ]);

            $factory->add('fat', [
                'path' => resource_path('svg/thin'),
                'prefix' => 'fat',
            ]);
        });
    }

    public function boot(): void {}
}
