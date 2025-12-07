<?php

namespace Packages\Course\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Course\Models\Course;
use Packages\Course\Models\CourseChapter;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Course\Models\CourseChapter>
 */
class CourseChapterFactory extends Factory
{
    protected $model = CourseChapter::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence();

        return [
            'title'     => $title,
            'course_id' => Course::factory(),
        ];
    }
}
