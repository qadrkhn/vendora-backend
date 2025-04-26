<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\File\FileController;


Route::prefix('v1')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('/files/upload', [FileController::class, 'store']);
    });
});
