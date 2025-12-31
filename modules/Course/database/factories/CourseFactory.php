<?php

namespace Modules\Course\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Course\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Course\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

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
            'slug'         => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'difficulty'   => $this->faker->randomElement(['BEGINNER', 'MEDIUM', 'ADVANCED']),
            'excerpt'      => $this->faker->paragraph(2),
            'content'      => $this->faker->paragraphs(5, true),
            'status'       => $this->faker->randomElement(['DRAFT', 'PUBLISHED', 'PENDING']),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'user_id'      => User::factory(),
        ];
    }
}
