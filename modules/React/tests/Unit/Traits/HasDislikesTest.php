<?php

namespace Modules\React\Tests\Unit\Traits;

use Modules\React\Traits\HasDislikes;
use Modules\User\Models\User;
use Tests\TestCase;

class HasDislikesTest extends TestCase
{
    public function test_can_dislike_and_check_is_disliked(): void
    {
        $user     = User::factory()->create();
        $disliker = User::factory()->create();

        $model = new class extends User {
            use HasDislikes;

            protected $table = 'users';
        };
        $model->id     = $user->id;
        $model->exists = true;

        $model->dislike($disliker->id);

        $this->assertTrue($model->isDislikedBy($disliker->id));
        $this->assertEquals(1, $model->dislikesCount());

        $model->undislike($disliker->id);

        $this->assertFalse($model->isDislikedBy($disliker->id));
        $this->assertEquals(0, $model->dislikesCount());
    }

    public function test_toggle_dislike(): void
    {
        $user     = User::factory()->create();
        $disliker = User::factory()->create();

        $model = new class extends User {
            use HasDislikes;

            protected $table = 'users';
        };
        $model->id     = $user->id;
        $model->exists = true;

        $model->toggleDislike($disliker->id);
        $this->assertTrue($model->isDislikedBy($disliker->id));

        $model->toggleDislike($disliker->id);
        $this->assertFalse($model->isDislikedBy($disliker->id));
    }
}
