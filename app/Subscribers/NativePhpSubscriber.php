<?php

declare(strict_types=1);

namespace App\Subscribers;

use App\Events\OpenCommandPaletteEvent;
use App\Events\OpenPreferencesEvent;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Str;
use Native\Laravel\Events\App\ApplicationBooted;
use Native\Laravel\Events\Windows\WindowBlurred;
use Native\Laravel\Events\Windows\WindowClosed;
use Native\Laravel\Events\Windows\WindowFocused;
use Native\Laravel\Events\Windows\WindowHidden;
use Native\Laravel\Facades\GlobalShortcut;
use Native\Laravel\Facades\Window;
use XbNz\Fping\DTOs\CreateFpingPreferencesDto;
use XbNz\Fping\Jobs\CreateFpingPreferencesJob;
use XbNz\Fping\Models\FpingPreferences;
use XbNz\Shared\Attributes\ListensTo;
use XbNz\Shared\Enums\NativePhpWindow;

final class NativePhpSubscriber
{
    public function __construct(
        private readonly Dispatcher $dispatcher
    ) {}

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
            ->height(300)
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
        if (FpingPreferences::query()->exists() === true) {
            return;
        }

        $this->dispatcher->dispatch(new CreateFpingPreferencesJob(
            new CreateFpingPreferencesDto(
                Str::random(5),
                56,
                1.5,
                1,
                64,
                100,
                500,
                '0x00',
                0,
                500,
                false,
                false
            )
        ));
    }
}
