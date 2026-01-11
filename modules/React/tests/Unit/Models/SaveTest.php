<?php

namespace Modules\React\Tests\Unit\Models;

use Modules\React\Models\Save;
use Modules\React\Tests\TestCase;

class SaveTest extends TestCase
{
    public function test_can_instantiate_save_model(): void
    {
        $target = \Modules\User\Models\User::factory()->create();
        $save   = Save::factory()->create([
            'saveable_id'   => $target->id,
            'saveable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Save::class, $save);
    }
}
