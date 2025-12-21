<?php

use Modules\Search\Http\Controllers\SearchController;

Route::controller(SearchController::class)->group(function () {
    Route::get('/', 'view')->name('view');
});
