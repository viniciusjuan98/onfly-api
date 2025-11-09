<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TravelOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', [HealthController::class, 'ping']);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    Route::get('me/notificacoes', [NotificationController::class, 'index']);
    Route::patch('me/notificacoes/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::post('orders', [TravelOrderController::class, 'store']);
    Route::get('orders', [TravelOrderController::class, 'index']);
    Route::get('orders/{id}', [TravelOrderController::class, 'show']);

    Route::middleware('admin')->group(function () {
        Route::patch('orders/{id}/status', [TravelOrderController::class, 'updateStatus']);
    });
});
