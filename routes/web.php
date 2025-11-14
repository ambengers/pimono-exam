<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TransactionsController;

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    
    Route::get('/reset-password/{token}', [SpaController::class, '__invoke'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth', 'json'])->group(function () {
    Route::group(['prefix' => 'api', 'as' => 'api.'],  function () {
        Route::get('/accounts', [AccountsController::class, 'index'])->name('accounts.index');

        Route::get('transactions', [TransactionsController::class, 'index'])->name('transactions.index');
        Route::post('transactions', [TransactionsController::class, 'store'])->name('transactions.store');
        Route::get('transactions/{transaction}', [TransactionsController::class, 'show'])->name('transactions.show');
    });


    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/auth/user', [AuthController::class, 'user']);

Route::get('/{any}', SpaController::class)->where('any', '.*');
