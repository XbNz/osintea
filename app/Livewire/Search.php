<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Support\Str;
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

        Window::open(NativePhpWindow::Ping->value.Str::random(8))
            ->route('ping')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->transparent()
            ->height(495)
            ->width(775)
            ->minHeight(495)
            ->minWidth(775);
    }

    public function render(): View
    {
        return view('livewire.search');
    }
}
