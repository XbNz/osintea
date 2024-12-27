<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\OpenCommandPaletteEvent;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\ChildProcess;
use Native\Laravel\Facades\GlobalShortcut;
use XbNz\Shared\Enums\NativePhpChildProcess;

final class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        GlobalShortcut::key('CmdOrCtrl+Shift+A')
            ->event(OpenCommandPaletteEvent::class)
            ->register();

        ChildProcess::artisan('ping:work', NativePhpChildProcess::PingWorker->value, persistent: true);
    }

    /**
     * Return an array of php.ini directives to be set.
     *
     * @return array<string, string>
     */
    public function phpIni(): array
    {
        return [
            'opcache.enable' => '1',
            'opcache.enable_cli' => '1',
            'opcache.jit' => 'tracing',
            'opcache.jit_buffer_size' => '128M',
            'memory_limit' => '1G',
            'max_execution_time' => 600,
        ];
    }
}
