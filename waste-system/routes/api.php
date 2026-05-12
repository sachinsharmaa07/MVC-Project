<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\PickupRequestController;
use Illuminate\Support\Facades\Route;

// Public auth endpoints
Route::post('/tokens', [AuthController::class, 'createToken']);

Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/tokens/revoke', [AuthController::class, 'revokeToken']);
    Route::get('/tokens', [AuthController::class, 'listTokens']);
    Route::delete('/tokens/{tokenId}', [AuthController::class, 'deleteToken']);

    // Pickup request endpoints - Full CRUD
    Route::post('/pickup-requests', [PickupRequestController::class, 'store']);
    Route::get('/pickup-requests', [PickupRequestController::class, 'index']);
    Route::get('/pickup-requests/{id}', [PickupRequestController::class, 'show']);
    Route::patch('/pickup-requests/{id}', [PickupRequestController::class, 'update']);
    Route::delete('/pickup-requests/{id}', [PickupRequestController::class, 'destroy']);
    
    // Pickup request status endpoints
    Route::get('/pickup-requests/{id}/status', [PickupRequestController::class, 'status']);
    Route::patch('/pickup-requests/{id}/status', [PickupRequestController::class, 'updateStatus']);
    
    // Route and truck endpoints
    Route::get('/routes/{id}/stops', [PickupRequestController::class, 'routeStops']);
    Route::get('/trucks/{id}/location', [PickupRequestController::class, 'truckLocation']);

    // Analytics endpoints
    Route::get('/analytics/summary', [AnalyticsController::class, 'summary']);
});

