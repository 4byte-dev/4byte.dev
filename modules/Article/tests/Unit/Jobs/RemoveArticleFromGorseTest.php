<?php

namespace Modules\Article\Tests\Unit\Jobs;

use Mockery;
use Modules\Article\Jobs\RemoveArticleFromGorse;
use Modules\Article\Tests\TestCase;
use Modules\Recommend\Services\GorseService;

class RemoveArticleFromGorseTest extends TestCase
{
    public function test_job_deletes_item_from_gorse(): void
    {
        $articleId = 1;

        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteItem')
            ->once()
            ->with("article:{$articleId}");

        $job = new RemoveArticleFromGorse($articleId);
        $job->handle($gorseService);
    }
}
