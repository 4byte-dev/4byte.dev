<?php

namespace Modules\Article\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Models\Article;

class ArticleObserver
{
    /**
     * Handle the "saved" event for the Article model.
     */
    public function saved(Article $article): void
    {
        if ($article->status !== ArticleStatus::PUBLISHED) {
            return;
        }

        event(new ArticlePublishedEvent($article));
    }

    /**
     * Handle the "updated" event for the Article model.
     */
    public function updated(Article $article): void
    {
        if ($article->isDirty('slug')) {
            Cache::forget("article:{$article->getOriginal('slug')}:id");
        }

        Cache::forget("article:{$article->id}");
    }

    /**
     * Handle the "deleted" event for the Article model.
     */
    public function deleted(Article $article): void
    {
        event(new ArticleDeletedEvent($article));
        Cache::forget("article:{$article->slug}:id");
        Cache::forget("article:{$article->id}");
        Cache::forget("article:{$article->id}:likes");
        Cache::forget("article:{$article->id}:dislikes");
    }
}
