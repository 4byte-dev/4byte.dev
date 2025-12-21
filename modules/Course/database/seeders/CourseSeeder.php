<?php

namespace Modules\Course\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Course\Models\Course;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::factory(20)->create();
    }
}
