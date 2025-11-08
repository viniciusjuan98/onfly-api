<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TravelOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::post('orders', [TravelOrderController::class, 'store']);
    Route::get('orders', [TravelOrderController::class, 'index']);
    Route::get('orders/{id}', [TravelOrderController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::patch('orders/{id}/status', [TravelOrderController::class, 'updateStatus']);
    });
});
