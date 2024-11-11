<?php

declare(strict_types=1);

use XbNz\Ping\Livewire\Ping;


Route::middleware(['web'])->group(function () {
    Route::get('/ping', Ping::class)->name('ping');
});
