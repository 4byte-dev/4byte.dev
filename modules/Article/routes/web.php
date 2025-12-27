<?php

use Illuminate\Support\Facades\Route;
use Modules\Article\Http\Controllers\ArticleController;
use Modules\Article\Http\Controllers\ArticleCrudController;
use Modules\Article\Models\Article;

Route::controller(ArticleCrudController::class)->middleware('auth')->name('crud.')->group(function () {
    Route::get('/yaz', 'createView')->name('create.view')->can('create', Article::class);
    Route::get('/{article:slug}/duzenle', 'editView')->name('edit.view')->can('update,article');
});

Route::controller(ArticleController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
