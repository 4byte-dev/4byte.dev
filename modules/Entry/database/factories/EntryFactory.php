<?php

namespace Modules\Entry\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Entry\Models\Entry;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Entry\Models\Entry>
 */
class EntryFactory extends Factory
{
    protected $model = Entry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'slug'    => Str::uuid()->toString(),
            'content' => $this->faker->paragraphs(rand(2, 5), true),
            'user_id' => User::factory(),
        ];
    }
}
