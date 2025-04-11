<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;


Route::prefix('v1')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
