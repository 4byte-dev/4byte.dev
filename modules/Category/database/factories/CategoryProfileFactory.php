<?php

namespace Modules\Category\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProfile;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Category\Models\CategoryProfile>
 */
class CategoryProfileFactory extends Factory
{
    protected $model = CategoryProfile::class;

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
            'category_id' => Category::factory(),
        ];
    }
}
