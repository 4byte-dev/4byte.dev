<?php

namespace Modules\Article\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Jobs\RemoveArticleFromGorse;
use Modules\Article\Jobs\SyncArticleToGorse;
use Modules\Article\Models\Article;

class ArticleObserver
{
    /**
     * Handle the "saved" event for the Article model.
     */
    public function saved(Article $article): void
    {
        if ($article->status != "PUBLISHED") {
            return;
        }

        SyncArticleToGorse::dispatch($article);
    }

    /**
     * Handle the "updating" event for the Article model.
     */
    public function updating(Article $article): void
    {
        if ($article->isDirty('image')) {
            $oldMedia = $article->getFirstMedia('article');
            if ($oldMedia) {
                $oldMedia->delete();
            }
        }
    }

    /**
     * Handle the "updated" event for the Article model.
     */
    public function updated(Article $article): void
    {
        Cache::forget("article:{$article->id}");
    }

    /**
     * Handle the "deleted" event for the Article model.
     */
    public function deleted(Article $article): void
    {
        RemoveArticleFromGorse::dispatch($article->id);
        Cache::forget("article:{$article->slug}:id");
        Cache::forget("article:{$article->id}");
        Cache::forget("article:{$article->id}:likes");
        Cache::forget("article:{$article->id}:dislikes");
    }
}
