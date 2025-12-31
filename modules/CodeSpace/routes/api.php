<?php

use Illuminate\Support\Facades\Route;
use Modules\CodeSpace\Http\Controllers\CodeSpaceCrudController;
use Modules\CodeSpace\Models\CodeSpace;

Route::prefix('crud')
    ->name('crud.')
    ->controller(CodeSpaceCrudController::class)
    ->group(function () {
        Route::get('/list', 'list')
            ->name('list');
        Route::get('/{slug}/get', 'get')
            ->name('get');
        Route::post('/create', 'create')
            ->name('create')
            ->can('create', CodeSpace::class);
        Route::post('/{code:slug}/edit', 'edit')
            ->name('edit')
            ->can('update,code');
    });
