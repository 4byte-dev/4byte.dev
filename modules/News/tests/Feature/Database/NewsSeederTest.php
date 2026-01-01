<?php

namespace Modules\News\Tests\Feature\Database\Seeders;

use Modules\News\Database\Seeders\NewsSeeder;
use Modules\News\Models\News;
use Modules\News\Tests\TestCase;

class NewsSeederTest extends TestCase
{
    public function test_it_seeds_news(): void
    {
        $this->seed(NewsSeeder::class);

        $this->assertDatabaseCount(News::class, 20);
    }
}
