<?php

namespace Packages\Article\Tests\Feature\Database\Seeders;

use Packages\Article\Database\Seeders\ArticleSeeder;
use Packages\Article\Models\Article;
use Packages\Article\Tests\TestCase;

class ArticleSeederTest extends TestCase
{
    public function test_it_seeds_articles(): void
    {
        $this->seed(ArticleSeeder::class);

        $this->assertDatabaseCount(Article::class, 20);
    }
}
