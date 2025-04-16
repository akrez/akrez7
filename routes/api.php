<?php

use App\Http\Controllers\Api\SummaryController;
use App\Http\Controllers\Api\TelegramMessageController;
use Illuminate\Support\Facades\Route;

Route::get('/summaries/{blog_id}', [SummaryController::class, 'index']);
Route::post('/summaries/{blog_id}/telegram_messages/{telegram_token}/webhook', [TelegramMessageController::class, 'webhook'])->name('telegram_messages.webhook');
