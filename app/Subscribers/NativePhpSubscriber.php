<?php

declare(strict_types=1);

namespace App\Subscribers;

use App\Events\OpenCommandPaletteEvent;
use App\Events\OpenPreferencesEvent;
use App\Steps\OnBooted\CreateDefaultFpingPreferences;
use App\Steps\OnBooted\CreateDefaultMasscanPreferences;
use App\Steps\OnBooted\EnableDefaultFpingPreferences;
use App\Steps\OnBooted\EnableDefaultMasscanPreferences;
use App\Steps\OnBooted\Transporter;
use Native\Desktop\Events\App\ApplicationBooted;
use Native\Desktop\Events\Windows\WindowBlurred;
use Native\Desktop\Events\Windows\WindowClosed;
use Native\Desktop\Events\Windows\WindowFocused;
use Native\Desktop\Events\Windows\WindowHidden;
use Native\Desktop\Facades\GlobalShortcut;
use Native\Desktop\Facades\Window;
use XbNz\Shared\Attributes\ListensTo;
use XbNz\Shared\Enums\NativePhpWindow;
use XbNz\Shared\Pipeline;

final class NativePhpSubscriber
{
    #[ListensTo(OpenCommandPaletteEvent::class)]
    public function onOpenCommandPalette(): void
    {
        Window::open(NativePhpWindow::CommandPalette->value)
            ->route('search')
            ->alwaysOnTop()
            ->transparent()
            ->resizable(false)
            ->closable(true)
            ->frameless()
            ->showDevTools(false)
            ->height(500)
            ->width(600)
            ->minHeight(300)
            ->minWidth(600);
    }

    #[ListensTo(OpenPreferencesEvent::class)]
    public function onOpenPreferences(): void
    {
        Window::open(NativePhpWindow::Preferences->value)
            ->route('preferences')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->transparent()
            ->height(640)
            ->width(820)
            ->minHeight(640)
            ->minWidth(820);
    }

    #[ListensTo(WindowFocused::class)]
    public function onWindowFocused(): void
    {
        GlobalShortcut::key('CmdOrCtrl+,')
            ->event(OpenPreferencesEvent::class)
            ->register();
    }

    #[ListensTo(WindowClosed::class)]
    #[ListensTo(WindowBlurred::class)]
    #[ListensTo(WindowHidden::class)]
    public function onWindow(): void
    {
        GlobalShortcut::key('CmdOrCtrl+,')->unregister();
    }

    #[ListensTo(ApplicationBooted::class)]
    public function onBooted(): void
    {
        $pipes = [
            CreateDefaultFpingPreferences::class,
            EnableDefaultFpingPreferences::class,
            CreateDefaultMasscanPreferences::class,
            EnableDefaultMasscanPreferences::class,
        ];

        Pipeline::make()
            ->send(new Transporter)
            ->through($pipes)
            ->thenReturn();
    }
}
