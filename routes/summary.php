<?php

use App\Http\Controllers\SummaryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SummaryController::class, 'show'])->name('show');
Route::get('/sitemap.xml', [SummaryController::class, 'sitemap'])->name('sitemap');
