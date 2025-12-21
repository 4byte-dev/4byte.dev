<?php

use Modules\React\Http\Controllers\ReactController;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Follow;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

Route::controller(ReactController::class)->group(function () {
    Route::middleware('auth')->group(function () {
        Route::post('/{type}/{slug}/like', 'like')->name('like')->can('create', Like::class);
        Route::post('/{type}/{slug}/dislike', 'dislike')->name('dislike')->can('create', Dislike::class);
        Route::post('/{type}/{slug}/save', 'save')->name('save')->can('create', Save::class);
        Route::post('/{type}/{slug}/comment', 'comment')->name('comment')->can('create', Comment::class);
        Route::post('/{type}/{slug}/follow', 'follow')->name('follow')->can('create', Follow::class);
    });
    Route::post('/{type}/{slug}/comments', 'comments')
        ->name('comments');
    Route::post('/{type}/{slug}/comment/{parent}/replies', 'replies')
        ->name('comment.replies');
});
