<?php

namespace Packages\Course\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Course\Models\CourseChapter;
use Packages\Course\Models\CourseLesson;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Course\Models\CourseLesson>
 */
class CourseLessonFactory extends Factory
{
    protected $model = CourseLesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence();

        return [
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . Str::random(6),
            'content'      => $this->faker->paragraphs(3, true),
            'video_url'    => $this->faker->optional()->url(),
            'status'       => $this->faker->randomElement(['DRAFT', 'PUBLISHED', 'PENDING']),
            'published_at' => now(),
            'user_id'      => User::factory(),
            'chapter_id'   => CourseChapter::factory(),
        ];
    }
}
