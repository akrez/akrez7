<?php

use App\Enums\PresenterEnum;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PayvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPropertyController;
use App\Http\Controllers\ProductTagController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TelegramBotController;
use App\Http\Middleware\CheckActiveBlogMiddleware;
use App\Http\Middleware\CheckPresenterMiddleware;
use App\Providers\AppServiceProvider;
use App\Services\DomainService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$domains = DomainService::new()->getDomainsArray()->getData('domains');
if ($domains) {
    Route::domain('{domain}')
        ->whereIn('domain', $domains)
        ->middleware(CheckPresenterMiddleware::class.':'.PresenterEnum::DOMAIN->value)
        ->as('domain.')
        ->group(function () {
            include 'present.php';
        });
}

Auth::routes();

Route::middleware('auth')->group(function () {
    //
    Route::get(AppServiceProvider::HOME, [BlogController::class, 'index'])->name('home');
    Route::patch('blogs/{id}/active', [BlogController::class, 'active'])->name('blogs.active');
    Route::resource('blogs', BlogController::class)->parameter('blogs', 'id')->except(['show', 'destroy']);
    //
    Route::middleware(CheckActiveBlogMiddleware::class)->group(function () {
        Route::resource('invoices', InvoiceController::class)->parameter('invoices', 'id')->only(['index', 'update']);
        //
        Route::prefix('preview/{blog_id}')
            ->middleware(CheckPresenterMiddleware::class.':'.PresenterEnum::PREVIEW->value)
            ->as('preview.')
            ->group(function () {
                include 'present.php';
            });
        //
        Route::get('payvoices', [PayvoiceController::class, 'index'])->name('payvoices.index');
        //
        Route::get('galleries/index/{gallery_category}/{gallery_id}', [GalleryController::class, 'index'])->name('galleries.index');
        Route::resource('galleries', GalleryController::class)->parameter('galleries', 'id')->except(['index', 'show']);
        //
        Route::post('telegram_bots/{id}/upload', [TelegramBotController::class, 'upload'])->name('telegram_bots.upload');
        Route::resource('telegram_bots', TelegramBotController::class)->parameter('telegram_bots', 'id');
        Route::resource('colors', ColorController::class)->parameter('colors', 'id');
        Route::resource('contacts', ContactController::class)->parameter('contacts', 'id');
        Route::resource('products', ProductController::class)->parameter('products', 'id');
        //
        Route::resource('products/{product_id}/packages', PackageController::class)->parameter('packages', 'id')->names('products.packages');
        Route::get('products/{product_id}/product_tags', [ProductTagController::class, 'create'])->name('products.product_tags.create');
        Route::post('products/{product_id}/product_tags', [ProductTagController::class, 'store'])->name('products.product_tags.store');
        //
        Route::get('products/{product_id}/product_properties', [ProductPropertyController::class, 'create'])->name('products.product_properties.create');
        Route::post('products/{product_id}/product_properties', [ProductPropertyController::class, 'store'])->name('products.product_properties.store');
    });
});

Route::get('/', [SiteController::class, 'index'])->name('site');
Route::get('/gallery/{gallery_category}/{whmq}/{name}', [GalleryController::class, 'effect']);

Route::prefix('front/{blog_id}')
    ->middleware(CheckPresenterMiddleware::class.':'.PresenterEnum::FRONT->value)
    ->as('front.')
    ->group(function () {
        include 'present.php';
    });
