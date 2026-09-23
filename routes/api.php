<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| Routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group.
|
*/

// Authentication Routes (version-agnostic)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// User Route (version-agnostic)
Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum')->name('user');

// API Status/Health Check
Route::get('status', function () {
    return response()->json([
        'service' => 'Dev Hub API',
        'status' => 'online',
        'timestamp' => now()->toISOString(),
        'versions' => [
            'v1' => ['status' => 'active', 'deprecated' => false],
            'v2' => ['status' => 'development', 'deprecated' => false],
        ],
    ]);
})->name('status');

// Version 1 Routes
Route::prefix('v1')->name('v1.')->middleware('auth:sanctum')->group(function () {
    require __DIR__.'/api/v1.php';
});

// Version 2 Routes (Future - currently disabled to avoid unnecessary middleware overhead)
// Route::prefix('v2')->name('v2.')->middleware('auth:sanctum')->group(function () {
//     require __DIR__.'/api/v2.php';
// });
