<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use XbNz\Location\Livewire\LocationToRange;

Route::middleware(['web'])->group(function (): void {
    Route::get('/location-to-range', LocationToRange::class)->name('location-to-range');
});
