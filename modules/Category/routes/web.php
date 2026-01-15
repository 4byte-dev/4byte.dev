<?php

use Modules\Category\Http\Controllers\CategoryController;

Route::controller(CategoryController::class)->group(function () {
    Route::get('/search', 'search')->name('search');
    Route::get('/{slug}', 'view')->name('view');
});
