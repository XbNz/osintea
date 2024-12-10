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

        $uniqueId = Str::random(8);

        Window::open(NativePhpWindow::Ping->value.':'.$uniqueId)
            ->route('ping')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->transparent()
            ->height(500)
            ->width(775)
            ->minHeight(500)
            ->minWidth(775);
    }

    public function openIpAddresses(): void
    {
        Window::close(NativePhpWindow::CommandPalette->value);

        Window::open(NativePhpWindow::IpAddresses->value)
            ->route('ip-addresses.index')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->transparent()
            ->height(1000)
            ->width(900)
            ->minHeight(1000)
            ->minWidth(900);
    }

    public function openRangeToIp(): void
    {
        Window::close(NativePhpWindow::CommandPalette->value);

        Window::open(NativePhpWindow::RangeToIp->value)
            ->route('range-to-ip')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->transparent()
            ->height(550)
            ->width(500)
            ->minHeight(550)
            ->minWidth(500);
    }

    public function openOrganizationToRange(): void
    {
        Window::close(NativePhpWindow::CommandPalette->value);

        Window::open(NativePhpWindow::OrganizationToRange->value)
            ->route('organization-to-range')
            ->showDevTools(false)
            ->titleBarHiddenInset()
            ->transparent()
            ->height(462)
            ->width(500)
            ->minHeight(462)
            ->minWidth(500);
    }

    public function render(): View
    {
        return view('livewire.search');
    }
}
