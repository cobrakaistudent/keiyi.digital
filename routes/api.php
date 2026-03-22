<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// William / Blog — Command Center (Sanctum token local)
Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('posts')->group(function () {
    Route::get('/pending',          [App\Http\Controllers\Api\PostApiController::class, 'pending']);
    Route::post('/{id}/approve',    [App\Http\Controllers\Api\PostApiController::class, 'approve']);
    Route::post('/{id}/publish',    [App\Http\Controllers\Api\PostApiController::class, 'publish']);
    Route::post('/{id}/reject',     [App\Http\Controllers\Api\PostApiController::class, 'reject']);
});

// Rutas para el Keiyi Brain Hub (Local Mac Agent)
// ⚠️ ATENCIÓN: Por mandato arquitectónico (#1427), el Command Center opera
// exclusivamente vía SSH Directo y transferencias SCP hacia la base de datos.
// Se han deshabilitado estas APIs públicas para cerrar la superficie de ataque.
/*
Route::middleware('auth:sanctum')->group(function () {
    
    // Scout AI
    Route::prefix('scout')->group(function() {
        Route::get('/pending',  [ScoutApiController::class, 'getPendingSources']);
        Route::get('/insights', [ScoutApiController::class, 'getInsights']);
        Route::post('/insight', [ScoutApiController::class, 'receiveInsight']);
    });

    // Gestión de Alumnos Remota (Command Center)
    Route::prefix('users')->group(function() {
        Route::get('/pending', [UserApiController::class, 'getPendingUsers']);
        Route::post('/{id}/status', [UserApiController::class, 'updateStatus']);
    });
});
*/
