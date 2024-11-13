<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Laravel\Facades\Window;
use XbNz\Shared\Enums\NativePhpWindow;

#[Layout('components.layouts.transparent')]
final class Search extends Component
{
    public function closeCommandPalette(): void
    {
        Window::close(NativePhpWindow::CommandPalette->value);
    }

    public function openPing(): void
    {
        Window::close(NativePhpWindow::CommandPalette->value);

        Window::open(NativePhpWindow::Ping->value)
            ->route('ping')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->height(450)
            ->width(535)
            ->minHeight(450)
            ->minWidth(535);
    }

    public function render(): View
    {
        return view('livewire.search');
    }
}
