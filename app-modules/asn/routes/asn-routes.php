<?php

declare(strict_types=1);

// use XbNz\Asn\Http\Controllers\AsnController;

use XbNz\Asn\Livewire\OrganizationToRange;

Route::middleware(['web'])->group(function (): void {
    Route::get('/organization-to-range', OrganizationToRange::class)->name('organization-to-range');
});
