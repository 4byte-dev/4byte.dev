<?php

namespace Packages\Tag\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Packages\Category\Models\Category;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Packages\Tag\Models\TagProfile>
 */
class TagProfileFactory extends Factory
{
    protected $model = TagProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->paragraph(),
            'color'       => $this->faker->hexColor(),
            'tag_id'      => Tag::factory(),
        ];
    }

    /**
     * Attach categories to tag profile.
     *
     * @param int $count
     *
     * @return static
     */
    public function withCategories(int $count = 1): static
    {
        return $this->afterCreating(function (TagProfile $tagProfile) use ($count) {
            $categories = Category::factory()->count($count)->create();

            $tagProfile->categories()->attach(
                $categories->pluck('id')
            );
        });
    }

    /**
     * Attach a specific category.
     *
     * @param Category|int $category
     *
     * @return static
     */
    public function withCategory($category): static
    {
        return $this->afterCreating(function (TagProfile $tagProfile) use ($category) {

            if ($category instanceof Category) {
                $categoryId = $category->id;
            } elseif (is_numeric($category)) {
                $categoryId = $category;
            }

            $tagProfile->categories()->attach($categoryId);
        });
    }
}
