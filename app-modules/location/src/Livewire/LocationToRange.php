<?php

declare(strict_types=1);

namespace XbNz\Location\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.secondary-window')]
final class LocationToRange extends Component
{
    public function addPolygon(array $geoJson): void {}

    public function render()
    {
        return view('location::livewire.location-to-range');
    }
}
