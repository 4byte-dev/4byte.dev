<?php

namespace Modules\React\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Modules\React\Models\Save;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\React\Models\Save>
 */
class SaveFactory extends Factory
{
    protected $model = Save::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'       => User::factory(),
            'saveable_id'   => null,
            'saveable_type' => null,
        ];
    }

    public function forModel(Model $model): SaveFactory
    {
        return $this->state(function () use ($model) {
            return [
                'saveable_id'   => $model->id, /* @phpstan-ignore-line */
                'saveable_type' => get_class($model),
            ];
        });
    }
}
