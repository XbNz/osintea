<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.secondary-window')]
final class Preferences extends Component
{
    public function render(): View
    {
        return view('livewire.preferences');
    }
}
