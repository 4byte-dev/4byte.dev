<?php

namespace Modules\React\Tests\Unit\Traits;

use Modules\React\Traits\HasLikes;
use Modules\User\Models\User;
use Tests\TestCase;

class HasLikesTest extends TestCase
{
    public function test_can_like_and_check_is_liked(): void
    {
        $user  = User::factory()->create();
        $liker = User::factory()->create();

        $model = new class extends User {
            use HasLikes;

            protected $table = 'users';
        };

        $model->id     = $user->id;
        $model->exists = true;

        $model->like($liker->id);

        $this->assertTrue($model->isLikedBy($liker->id));
        $this->assertEquals(1, $model->likesCount());

        $model->unlike($liker->id);

        $this->assertFalse($model->isLikedBy($liker->id));
        $this->assertEquals(0, $model->likesCount());
    }

    public function test_toggle_like(): void
    {
        $user  = User::factory()->create();
        $liker = User::factory()->create();

        $model = new class extends User {
            use HasLikes;

            protected $table = 'users';
        };
        $model->id     = $user->id;
        $model->exists = true;

        $model->toggleLike($liker->id);
        $this->assertTrue($model->isLikedBy($liker->id));

        $model->toggleLike($liker->id);
        $this->assertFalse($model->isLikedBy($liker->id));
    }
}
