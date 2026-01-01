<?php

namespace Modules\Course\Tests\Feature\Database\Seeders;

use Modules\Course\Database\Seeders\CourseSeeder;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Tests\TestCase;
use Modules\React\Models\Dislike;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

class DatabaseSeederTest extends TestCase
{
    public function test_it_seeds_courses_with_relations(): void
    {
        $this->seed(CourseSeeder::class);

        $this->assertDatabaseCount(Course::class, 3);
        $this->assertDatabaseCount(CourseChapter::class, 15);
        $this->assertDatabaseCount(CourseLesson::class, 105);
        $this->assertDatabaseCount(Like::class, 9);
        $this->assertDatabaseCount(Dislike::class, 9);
        $this->assertDatabaseCount(Save::class, 9);
    }
}
