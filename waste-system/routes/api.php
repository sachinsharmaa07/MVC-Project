<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\PickupRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pickup-requests', [PickupRequestController::class, 'store']);
    Route::get('/pickup-requests/{id}/status', [PickupRequestController::class, 'status']);
    Route::patch('/pickup-requests/{id}/status', [PickupRequestController::class, 'updateStatus']);
    Route::get('/routes/{id}/stops', [PickupRequestController::class, 'routeStops']);
    Route::get('/trucks/{id}/location', [PickupRequestController::class, 'truckLocation']);
    Route::get('/analytics/summary', [AnalyticsController::class, 'summary']);
});
