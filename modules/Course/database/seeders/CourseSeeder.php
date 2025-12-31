<?php

namespace Modules\Course\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\React\Models\Dislike;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        Course::factory()
            ->count(3)
            ->has(
                CourseChapter::factory()
                    ->count(5)
                    ->has(
                        CourseLesson::factory()->count(7),
                        'lessons'
                    ),
                'chapters'
            )
            ->has(Like::factory()->count(3), 'likes')
            ->has(Dislike::factory()->count(3), 'dislikes')
            ->has(Save::factory()->count(3), 'saves')
            ->create();
    }
}
