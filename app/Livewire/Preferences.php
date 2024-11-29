<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.secondary-window')]
final class Preferences extends Component
{
    public function render()
    {
        return view('livewire.preferences');
    }
}
