<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);