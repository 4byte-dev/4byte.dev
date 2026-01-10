<?php

use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\ArticleController;
use Modules\Article\Http\Controllers\ArticleCrudController;
use Modules\Article\Models\Article;

Route::controller(ArticleCrudController::class)->middleware('auth')->group(function () {
    Route::get('/yaz', 'create')->name('create')->can('create', Article::class);
    Route::get('/{article:slug}/duzenle', 'edit')->name('edit')->can('update,article');
});

Route::controller(ArticleController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
