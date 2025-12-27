<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\AuthController;
use Modules\User\Http\Controllers\UserController;

Route::get('/@{username}', [UserController::class, 'view'])->name('user.view');

Route::middleware('auth')
    ->prefix('user')
    ->name('user.')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/me/settings', 'settingsView')->name('settings.view');
        Route::get('/me/verification', 'verificationView')->name('verification.view');
        Route::middleware('signed')
            ->get('/me/verification/verify/{id}/{hash}', 'verificationVerify')
            ->name('verification.verify');
    });

Route::get('/auth/reset-password', [AuthController::class, 'viewResetPassword'])->name('auth.reset-password.view');
