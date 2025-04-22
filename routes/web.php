<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Horizon\HorizonAdminAuthController;


// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Web auth routes
Route::get('/login', [HorizonAdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [HorizonAdminAuthController::class, 'login']);
Route::post('/logout', [HorizonAdminAuthController::class, 'logout'])->name('logout');
