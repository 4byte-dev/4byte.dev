<?php

namespace Modules\React\Tests\Unit\Database\Factories;

use Modules\React\Database\Factories\SaveFactory;
use Modules\React\Models\Save;
use Modules\React\Tests\TestCase;

class SaveFactoryTest extends TestCase
{
    public function test_can_create_save_instance(): void
    {
        $factory = SaveFactory::new();
        $this->assertInstanceOf(SaveFactory::class, $factory);
    }

    public function test_can_create_save(): void
    {
        $target = \Modules\User\Models\User::factory()->create();
        $save   = Save::factory()->create([
            'saveable_id'   => $target->id,
            'saveable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Save::class, $save);
    }
}
