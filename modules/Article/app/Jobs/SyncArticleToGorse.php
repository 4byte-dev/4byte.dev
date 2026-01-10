<?php

namespace Modules\Article\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Article\Models\Article;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class SyncArticleToGorse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly Article $article)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(GorseService $gorse): void
    {
        $gorseItem = new GorseItem(
            "article:{$this->article->id}",
            ['article', "user:{$this->article->user_id}"],
            $this->article->tags->pluck('id')
                ->map(fn ($id) => "tag:{$id}")
                ->merge(
                    $this->article->categories->pluck('id')
                        ->map(fn ($id) => "category:{$id}")
                )
                ->merge(['article', "user:{$this->article->user_id}"])
                ->all(),
            $this->article->slug,
            false,
            Carbon::parse($this->article->published_at)->toDateTimeString()
        );

        $gorse->insertItem($gorseItem);
    }
}
