<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ScoutApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas para el Keiyi Brain Hub (Local Mac Agent)
Route::middleware('auth:sanctum')->prefix('scout')->group(function () {
    Route::get('/pending', [ScoutApiController::class, 'getPendingSources']);
    Route::post('/insight', [ScoutApiController::class, 'receiveInsight']);
});
