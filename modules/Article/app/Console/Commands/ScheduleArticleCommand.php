<?php

namespace Modules\Article\Console\Commands;

use Illuminate\Console\Command;
use Modules\Article\Jobs\PublishArticleJob;
use Modules\Article\Models\Article;

class ScheduleArticleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish pending articles';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Article::query()
            ->where('status', 'PENDING')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->chunkById(100, function ($articles) {
                foreach ($articles as $article) {
                    PublishArticleJob::dispatch($article);
                }
            });

        $this->info('Pending articles checked');
    }
}
