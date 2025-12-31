<?php

namespace Modules\CodeSpace\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\CodeSpace\Models\CodeSpace;

class CodeSpaceSeeder extends Seeder
{
    public function run(): void
    {
        CodeSpace::factory()->count(5)->create();
    }
}
