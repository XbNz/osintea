<?php

declare(strict_types=1);

namespace XbNz\Location\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.secondary-window')]
final class LocationToRange extends Component
{
    /**
     * @param  array<mixed>  $geoJson
     */
    public function addPolygon(array $geoJson): void {}

    public function render(): View
    {
        return view('location::livewire.location-to-range');
    }
}
