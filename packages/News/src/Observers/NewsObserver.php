<?php

namespace Packages\News\Observers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Packages\News\Models\News;
use Packages\Recommend\Classes\GorseItem;
use Packages\Recommend\Services\GorseService;

class NewsObserver
{
    protected GorseService $gorse;

    public function __construct(GorseService $gorse)
    {
        $this->gorse = $gorse;
    }

    /**
     * Handle the "saved" event for the News model.
     */
    public function saved(News $news): void
    {
        $gorseItem = new GorseItem(
            'news:' . $news->id,
            ['news', "user:{$news->user_id}"],
            $news->tags->pluck('id')
                ->map(fn ($id) => 'tag:' . $id)
                ->merge(
                    $news->categories->pluck('id')
                        ->map(fn ($id) => 'category:' . $id)
                )
                ->merge(['news', "user:{$news->user_id}"])
                ->all(),
            $news->slug,
            $news->status != 'PUBLISHED',
            Carbon::parse($news->published_at)->toDateTimeString()
        );
        $this->gorse->insertItem($gorseItem);
    }

    /**
     * Handle the "updating" event for the News model.
     */
    public function updating(News $news): void
    {
        // Image handling is now managed by Spatie Media Library's singleFile() collection
    }

    /**
     * Handle the "updated" event for the News model.
     */
    public function updated(News $news): void
    {
        if ($news->isDirty('slug')) {
            Cache::forget("news:{$news->getOriginal('slug')}:id");
        }

        $gorseItem = new GorseItem(
            'news:' . $news->id,
            ['news', "user:{$news->user_id}"],
            $news->tags->pluck('id')
                ->map(fn ($id) => 'tag:' . $id)
                ->merge(
                    $news->categories->pluck('id')
                        ->map(fn ($id) => 'category:' . $id)
                )
                ->merge(['news', "user:{$news->user_id}"])
                ->all(),
            $news->slug,
            $news->status != 'PUBLISHED',
            Carbon::parse($news->published_at)->toDateTimeString()
        );

        $this->gorse->updateItem($gorseItem);
        Cache::forget("news:{$news->id}");
    }

    /**
     * Handle the "deleted" event for the News model.
     */
    public function deleted(News $news): void
    {
        $this->gorse->deleteItem('news:' . $news->id);
        Cache::forget("news:{$news->slug}:id");
        Cache::forget("news:{$news->id}");
    }
}
