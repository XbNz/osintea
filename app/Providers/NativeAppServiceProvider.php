<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\OpenCommandPaletteEvent;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\GlobalShortcut;
use Native\Laravel\Facades\Window;
use XbNz\Shared\Enums\NativePhpWindow;

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

        Window::open(NativePhpWindow::Main->value)
            ->route('ping')
            ->rememberState()
            ->showDevTools(false)
            ->height(800)
            ->width(1100)
            ->minHeight(800)
            ->minWidth(1100);
    }

    /**
     * Return an array of php.ini directives to be set.
     *
     * @return array<string, string>
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
