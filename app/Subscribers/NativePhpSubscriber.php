<?php

declare(strict_types=1);

namespace App\Subscribers;

use App\Events\OpenCommandPaletteEvent;
use Native\Laravel\Facades\Window;
use XbNz\Shared\Attributes\ListensTo;
use XbNz\Shared\Enums\NativePhpWindow;

final class NativePhpSubscriber
{
    #[ListensTo(OpenCommandPaletteEvent::class)]
    public function onOpenCommandPalette(): void
    {
        Window::open(NativePhpWindow::CommandPalette->value)
            ->alwaysOnTop()
            ->transparent()
            ->resizable(false)
            ->closable(true)
            ->route('search')
            ->frameless()
            ->showDevTools(false)
            ->height(300)
            ->width(600)
            ->minHeight(300)
            ->minWidth(600);
    }
}
