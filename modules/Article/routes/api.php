<?php

use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\ArticleCrudController;
use Modules\Article\Models\Article;

Route::controller(ArticleCrudController::class)
    ->group(function () {
        Route::post('/', 'store')->name('store')->can('create', Article::class);
        Route::put('/{article:slug}', 'update')->name('update')->can('update', 'article');
    });
