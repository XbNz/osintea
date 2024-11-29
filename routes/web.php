<?php

declare(strict_types=1);

use App\Livewire\Preferences;
use App\Livewire\Search;
use Illuminate\Support\Facades\Route;

Route::get('/search', Search::class)->name('search');
Route::get('/preferences', Preferences::class)->name('preferences');
