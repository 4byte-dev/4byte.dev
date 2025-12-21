<?php

use Illuminate\Support\Facades\Route;
use Modules\Entry\Http\Controllers\EntryController;

Route::controller(EntryController::class)->group(function () {
    Route::get('/{slug}', 'view')->name('view');
});
