<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::patch('blogs/{id}/active', [BlogController::class, 'active'])->name('blogs.active');
Route::resource('blogs', BlogController::class)->parameter('blogs', 'id')->except(['show', 'destroy']);
