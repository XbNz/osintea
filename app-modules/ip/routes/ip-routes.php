<?php

declare(strict_types=1);

use XbNz\Ip\Livewire\ListIpAddresses;

Route::middleware(['web'])->group(function (): void {
    Route::get('/ip-addresses', ListIpAddresses::class)->name('ip-addresses.index');
});
