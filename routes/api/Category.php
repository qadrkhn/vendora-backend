<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Category\CategoryController;


Route::prefix('v1')->group(function () {

    Route::middleware('auth:api')->group(function () {
        Route::get('/category/test', [CategoryController::class, 'test']);
    });
});
