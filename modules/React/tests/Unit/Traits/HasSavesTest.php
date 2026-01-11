<?php

namespace Modules\React\Tests\Unit\Traits;

use Modules\React\Traits\HasSaves;
use Modules\User\Models\User;
use Tests\TestCase;

class HasSavesTest extends TestCase
{
    public function test_can_save_and_check_is_saved(): void
    {
        $user  = User::factory()->create();
        $saver = User::factory()->create();

        $model = new class extends User {
            use HasSaves;

            protected $table = 'users';
        };
        $model->id     = $user->id;
        $model->exists = true;

        $model->saveFor($saver->id);

        $this->assertTrue($model->isSavedBy($saver->id));

        $model->unsave($saver->id);

        $this->assertFalse($model->isSavedBy($saver->id));
    }

    public function test_toggle_save(): void
    {
        $user  = User::factory()->create();
        $saver = User::factory()->create();

        $model = new class extends User {
            use HasSaves;

            protected $table = 'users';
        };
        $model->id     = $user->id;
        $model->exists = true;

        $model->toggleSave($saver->id);
        $this->assertTrue($model->isSavedBy($saver->id));

        $model->toggleSave($saver->id);
        $this->assertFalse($model->isSavedBy($saver->id));
    }
}
