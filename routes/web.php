<?php

use App\Http\Controllers\HomeController;
use App\Http\Middleware\HandleInertiaRequests;

Route::middleware([HandleInertiaRequests::class])->group(function () {
    Route::get('/', [HomeController::class, 'view'])->name('home.view');
});
