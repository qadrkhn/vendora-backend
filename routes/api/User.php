<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;


Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [UserController::class, 'me']);
    });
});
