<?php

declare(strict_types=1);

use App\Livewire\Test;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

