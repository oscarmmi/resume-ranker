<?php

use App\Http\Controllers\RankController;
use App\Http\Controllers\RankerPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', RankerPageController::class)->name('home');
Route::post('/rank', [RankController::class, 'store'])->name('rank.store');
