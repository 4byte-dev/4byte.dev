<?php

namespace Modules\React\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\React\Models\Comment;
use Modules\React\Policies\CommentPolicy;
use Modules\React\Tests\TestCase;

class CommentPolicyTest extends TestCase
{
    private CommentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CommentPolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_comment')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('view_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 1;

        $this->assertTrue($this->policy->view($user, $comment));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('view_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 2;

        $this->assertFalse($this->policy->view($user, $comment));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment = Mockery::mock(Comment::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $comment));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_comment')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('update_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 1;

        $this->assertTrue($this->policy->update($user, $comment));
    }

    public function test_update_others_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('update_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 2;

        $this->assertFalse($this->policy->update($user, $comment));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment = Mockery::mock(Comment::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $comment));
    }

    public function test_delete_own_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('delete_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $comment));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('delete_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $comment));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment = Mockery::mock(Comment::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $comment));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $comment));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $comment));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment = Mockery::mock(Comment::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $comment));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('restore_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $comment));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('restore_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $comment));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment = Mockery::mock(Comment::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $comment));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $comment));
    }

    public function test_replicate_others_comment(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_comment')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment          = Mockery::mock(Comment::class)->makePartial();
        $comment->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $comment));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_comment')->andReturn(true);

        /** @var Comment|MockInterface $comment */
        $comment = Mockery::mock(Comment::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $comment));
    }
}
