<?php

namespace Modules\Article\Tests\Feature\Database\Seeders;

use Modules\Article\Database\Seeders\ArticleSeeder;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

class ArticleSeederTest extends TestCase
{
    public function test_it_seeds_articles(): void
    {
        $this->seed(ArticleSeeder::class);

        $this->assertDatabaseCount(Article::class, 5);
        $this->assertDatabaseCount(Like::class, 15);
        $this->assertDatabaseCount(Dislike::class, 15);
        $this->assertDatabaseCount(Save::class, 15);
        $this->assertDatabaseCount(Comment::class, 15);
    }
}
