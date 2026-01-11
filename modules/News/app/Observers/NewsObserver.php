<?php

namespace Modules\News\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\News\Enums\NewsStatus;
use Modules\News\Events\NewsDeletedEvent;
use Modules\News\Events\NewsPublishedEvent;
use Modules\News\Models\News;

class NewsObserver
{
    /**
     * Handle the "saved" event for the News model.
     */
    public function saved(News $news): void
    {
        if ($news->status != NewsStatus::PUBLISHED) {
            return;
        }

        if ($news->wasRecentlyCreated || $news->isDirty('status')) {
            event(new NewsPublishedEvent($news));
        }
    }

    /**
     * Handle the "updated" event for the News model.
     */
    public function updated(News $news): void
    {
        if ($news->isDirty('slug')) {
            Cache::forget("news:{$news->getOriginal('slug')}:id");
        }

        Cache::forget("news:{$news->id}");
    }

    /**
     * Handle the "deleted" event for the News model.
     */
    public function deleted(News $news): void
    {
        event(new NewsDeletedEvent($news));
        Cache::forget("news:{$news->slug}:id");
        Cache::forget("news:{$news->id}");
    }
}
