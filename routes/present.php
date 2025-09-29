<?php

use App\Http\Controllers\PresentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PresentController::class, 'show'])->name('show');
Route::post('/invoices', [PresentController::class, 'storeInvoice'])->middleware('throttle:frontPost')->name('invoices.store');
Route::get('/invoices/{invoice_uuid}', [PresentController::class, 'showInvoice'])->name('invoices.show');
Route::get('/sitemap.xml', [PresentController::class, 'sitemap'])->name('sitemap');
