<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SiteController;
use App\Http\Middleware\CheckActiveBlogMiddleware;
use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::middleware('auth')->group(function () {
    //
    Route::get(AppServiceProvider::HOME, [BlogController::class, 'index'])->name('home');
    Route::patch('blogs/{id}/active', [BlogController::class, 'active'])->name('blogs.active');
    Route::resource('blogs', BlogController::class)->parameter('blogs', 'id')->except(['show', 'destroy']);
    //
    Route::middleware(CheckActiveBlogMiddleware::class)->group(function () {
        Route::get('galleries/index/{gallery_category}/{gallery_type}/{gallery_id}', [GalleryController::class, 'index'])->name('galleries.index');
        Route::resource('galleries', GalleryController::class)->parameter('galleries', 'id')->except(['index', 'show']);
    });
});

Route::get('/', [SiteController::class, 'index'])->name('site');
