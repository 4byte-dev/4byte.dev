<?php

use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\ArticleCrudController;
use Modules\Article\Models\Article;

Route::prefix('crud')
    ->name('crud.')
    ->controller(ArticleCrudController::class)
    ->group(function () {
        Route::post('/create', 'create')
            ->name('create')
            ->can('create', Article::class);

        Route::post('/{article:slug}/edit', 'edit')
            ->name('edit')
            ->can('update,article');
    });
