<?php

namespace Modules\Article\Console\Commands;

use Illuminate\Console\Command;
use Modules\Article\Actions\PublishArticleAction;
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
    public function handle(PublishArticleAction $publishArticleAction): void
    {
        Article::query()
            ->where('status', 'PENDING')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->chunkById(100, function ($articles) use ($publishArticleAction) {
                foreach ($articles as $article) {
                    $publishArticleAction->execute($article);
                }
            });

        $this->info('Pending articles checked');
    }
}
