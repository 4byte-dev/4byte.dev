<?php

namespace Packages\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Article\Models\Article;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::factory(20)->create();
    }
}
