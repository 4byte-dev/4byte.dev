<?php

use Illuminate\Support\Facades\Route;
use Modules\CodeSpace\Http\Controllers\CodeSpaceController;

Route::controller(CodeSpaceController::class)->group(function () {
    Route::get('/{slug?}', 'view')->name('view');
});
