<?php

use Modules\Category\Http\Controllers\CategoryController;

Route::controller(CategoryController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
