<?php

declare(strict_types=1);

use XbNz\Preferences\Livewire\Preferences;

Route::middleware(['web'])->group(function (): void {
    Route::get('/preferences', Preferences::class)->name('preferences');
});
