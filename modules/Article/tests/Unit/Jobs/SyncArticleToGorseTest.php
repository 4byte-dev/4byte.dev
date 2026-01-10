<?php

namespace Modules\Article\Tests\Unit\Jobs;

use Carbon\Carbon;
use Mockery;
use Modules\Article\Jobs\SyncArticleToGorse;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class SyncArticleToGorseTest extends TestCase
{
    public function test_job_inserts_item_to_gorse(): void
    {
        $article = Article::factory()->make(['id' => 1, 'user_id' => 1, 'published_at' => now(), 'slug' => 'test-slug']);
        $article->setRelation('tags', collect([]));
        $article->setRelation('categories', collect([]));

        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('insertItem')
            ->once()
            ->with(Mockery::on(function (GorseItem $item) use ($article) {
                return $item->getItemId() === 'article:' . $article->id
                    && $item->getLabels() === ['article', "user:{$article->user_id}"]
                    && $item->getTimestamp() === Carbon::parse($article->published_at)->toDateTimeString();
            }));

        $job = new SyncArticleToGorse($article);
        $job->handle($gorseService);
    }
}
