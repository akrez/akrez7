<?php

use App\Http\Controllers\Api\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/blogs/{id}', [BlogController::class, 'index']);
