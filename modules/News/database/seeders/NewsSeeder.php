<?php

namespace Modules\News\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\News\Models\News;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        News::factory(20)->create();
    }
}
