<?php

namespace Modules\Course\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Course\Models\CourseChapter>
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
