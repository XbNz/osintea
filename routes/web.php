<?php

declare(strict_types=1);

use XbNz\Preferences\Livewire\Preferences;
use App\Livewire\Search;
use Illuminate\Support\Facades\Route;

Route::get('/search', Search::class)->name('search');
