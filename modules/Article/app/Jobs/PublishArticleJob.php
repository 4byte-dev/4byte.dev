<?php

namespace Modules\Article\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Models\Article;

class PublishArticleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
    public function handle(): void
    {
        if ($this->article->status === 'PUBLISHED') {
            return;
        }

        $this->article->update(['status' => 'PUBLISHED']);
        event(new ArticlePublishedEvent($this->article));
    }
}
