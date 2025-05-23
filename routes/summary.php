<?php

use App\Http\Controllers\SummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SummaryController::class, 'show'])->name('show');
