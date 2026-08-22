<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'Laravel API çalışıyor'
    ]);
});

Route::get('/products', [ProductController::class, 'index']);

Route::get('/products/{product}', [ProductController::class, 'show']);