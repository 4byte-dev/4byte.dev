<?php

use Illuminate\Support\Facades\Route;
use Modules\Course\Http\Controllers\CourseController;

Route::controller(CourseController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
    Route::get('/{slug}/{page}', 'page')->name('page');
});
