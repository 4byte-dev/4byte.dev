<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;

Route::controller(PageController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
