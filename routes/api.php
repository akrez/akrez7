<?php

use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\TelegramMessageController;
use Illuminate\Support\Facades\Route;

Route::get('/blogs/{blog_id}', [BlogController::class, 'index']);
Route::post('/blogs/{blog_id}/telegram_messages/{telegram_token}/webhook', [TelegramMessageController::class, 'webhook'])->name('telegram_messages.webhook');
