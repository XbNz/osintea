<?php

declare(strict_types=1);

use XbNz\Ip\Livewire\ListIpAddresses;
use XbNz\Ip\Livewire\RangeToIp;

Route::middleware(['web'])->group(function (): void {
    Route::get('/ip-addresses', ListIpAddresses::class)->name('ip-addresses.index');
    Route::get('/range-to-ip', RangeToIp::class)->name('range-to-ip');
});
