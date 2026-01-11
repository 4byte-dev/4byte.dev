<?php

namespace Modules\Article\Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Article\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = $this->faker->unique()->sentence();

        return [
            'title'   => $title,
            'slug'    => Str::slug($title),
            'excerpt' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(5, true),
            'status'  => $this->faker->randomElement(ArticleStatus::cases()),
            'sources' => collect(range(0, rand(0, 5)))->map(function () {
                return [
                    'url'  => $this->faker->url(),
                    'date' => Carbon::instance(
                        $this->faker->dateTimeBetween('-6 months', '+6 months')
                    )->toDateTimeString(),
                ];
            })->toArray(),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'user_id'      => User::factory(),
        ];
    }
}
