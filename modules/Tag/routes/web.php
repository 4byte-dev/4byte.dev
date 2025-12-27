<?php

use Modules\Tag\Http\Controllers\TagController;

Route::controller(TagController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
