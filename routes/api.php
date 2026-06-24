<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('tracking.api')->prefix('tracking')->group(function () {
    Route::post('/upload', [\App\Http\Controllers\Api\ScreenshotTrackingController::class, 'upload']);
    Route::get('/config', [\App\Http\Controllers\Api\ScreenshotTrackingController::class, 'config']);
    Route::post('/heartbeat', [\App\Http\Controllers\Api\ScreenshotTrackingController::class, 'heartbeat']);
    Route::get('/today-screenshots', [\App\Http\Controllers\Api\ScreenshotTrackingController::class, 'todayScreenshots']);
});
