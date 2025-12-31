<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Article\Models\Article;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::factory()
            ->count(5)
            ->has(Like::factory()->count(3), 'likes')
            ->has(Dislike::factory()->count(3), 'dislikes')
            ->has(Save::factory()->count(3), 'saves')
            ->has(Comment::factory()->count(3), 'comments')
            ->create();
    }
}
