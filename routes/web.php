<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\Auth\AuthController;

// Authentication routes
Route::middleware('guest')->group(function () {
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth:web')->group(function () {
Route::post('/logout', [AuthController::class, 'logout']);
});

// Catch-all route for SPA - must be last
Route::get('/{any}', SpaController::class)->where('any', '.*');
