<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User endpoint for SPA authentication
Route::middleware('auth:web')->get('/user', function (Request $request) {
    return $request->user();
});

