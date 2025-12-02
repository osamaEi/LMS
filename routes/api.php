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

// API Version 1
Route::prefix('v1')->group(function () {

    // Public Routes
    Route::get('/programs', [App\Http\Controllers\Api\V1\ProgramController::class, 'index']);
    Route::get('/programs/{program}', [App\Http\Controllers\Api\V1\ProgramController::class, 'show']);

    // Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'register']);
        Route::post('/send-otp', [App\Http\Controllers\Api\V1\Auth\OtpController::class, 'send']);
        Route::post('/verify-otp', [App\Http\Controllers\Api\V1\Auth\OtpController::class, 'verify']);
        Route::post('/nafath/initiate', [App\Http\Controllers\Api\V1\Auth\NafathController::class, 'initiate']);
        Route::get('/nafath/poll/{transaction_id}', [App\Http\Controllers\Api\V1\Auth\NafathController::class, 'poll']);
        Route::post('/login', [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'login']);
        Route::post('/forgot-password', [App\Http\Controllers\Api\V1\Auth\PasswordResetController::class, 'forgot']);
        Route::post('/reset-password', [App\Http\Controllers\Api\V1\Auth\PasswordResetController::class, 'reset']);
    });

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth Routes (Protected)
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'logout']);
        });

        // Profile Routes
        Route::get('/profile', [App\Http\Controllers\Api\V1\Auth\LoginController::class, 'profile']);
        Route::post('/profile/complete', [App\Http\Controllers\Api\V1\ProfileController::class, 'completeProfile']);
    });
});
