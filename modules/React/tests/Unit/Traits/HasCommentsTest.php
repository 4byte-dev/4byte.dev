<?php

namespace Modules\React\Tests\Unit\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\React\Traits\HasComments;
use Modules\User\Models\User;
use Tests\TestCase;

class HasCommentsTest extends TestCase
{
    public function test_has_comments_relationship(): void
    {
        $model = new class extends User {
            use HasComments;

            protected $table = 'users';
        };

        $this->assertInstanceOf(MorphMany::class, $model->comments());
    }

    public function test_comments_count(): void
    {
        $user  = User::factory()->create();
        $model = new class extends User {
            use HasComments;

            protected $table = 'users';
        };
        $model->id     = $user->id;
        $model->exists = true;

        $this->assertEquals(0, $model->commentsCount());
    }
}
