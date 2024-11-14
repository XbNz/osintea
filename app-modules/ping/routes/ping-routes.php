<?php

declare(strict_types=1);

use XbNz\Ping\Livewire\Ping;
use XbNz\Ping\Livewire\PingResults;

Route::middleware(['web'])->group(function (): void {
    Route::get('/ping', Ping::class)->name('ping');
    Route::get('/ping-results', PingResults::class)->name('ping-results');
});
