<?php

use Illuminate\Support\Facades\Route;
use Modules\Entry\Http\Controllers\EntryCrudController;
use Modules\Entry\Models\Entry;

Route::prefix('crud')
    ->name('crud.')
    ->controller(EntryCrudController::class)
    ->group(function () {
        Route::post('/create', 'create')
            ->name('create')
            ->can('create', Entry::class);
    });
