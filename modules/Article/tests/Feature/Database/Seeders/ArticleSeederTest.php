<?php

namespace Modules\Article\Tests\Feature\Database\Seeders;

use Modules\Article\Database\Seeders\ArticleSeeder;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class ArticleSeederTest extends TestCase
{
    public function test_it_seeds_articles(): void
    {
        $this->seed(ArticleSeeder::class);

        $this->assertDatabaseCount(Article::class, 20);
    }
}
