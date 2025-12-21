<?php

namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Article\Models\Article;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::factory(20)->create();
    }
}
