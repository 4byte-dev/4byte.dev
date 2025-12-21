<?php

use Illuminate\Support\Facades\Route;
use Modules\Recommend\Http\Controllers\FeedController;

Route::controller(FeedController::class)->group(function () {
    Route::get('/', 'data')->name('data');
    Route::get('/feed', 'feed')->name('feed');
});
