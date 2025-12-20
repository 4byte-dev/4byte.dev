<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Route;
use Packages\User\Http\Controllers\AuthController;
use Packages\User\Http\Controllers\UserController;

Route::middleware([HandleInertiaRequests::class])->group(function () {
    Route::get('/@{username}', [UserController::class, 'view'])->name('user.view');

    Route::name('user.')->prefix('user')->controller(UserController::class)->middleware('auth')->group(function () {
        Route::get('/me/settings', 'settingsView')->name('settings.view');
        Route::get('/me/verification', 'verificationView')->name('verification.view');
        Route::middleware('signed')
            ->get('/me/verification/verify/{id}/{hash}', 'verificationVerify')
            ->name('verification.verify');
    });

    Route::get('/auth/reset-password', [AuthController::class, 'viewResetPassword'])->name('auth.reset-password.view');
});

require __DIR__ . '/api.php';
