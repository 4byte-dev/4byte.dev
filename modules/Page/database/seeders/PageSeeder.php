<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Page;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        Page::factory(10)->create();
    }
}
