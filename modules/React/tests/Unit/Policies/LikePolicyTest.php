<?php

namespace Modules\React\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\React\Models\Like;
use Modules\React\Policies\LikePolicy;
use Modules\React\Tests\TestCase;

class LikePolicyTest extends TestCase
{
    private LikePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new LikePolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_like')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('view_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 1;

        $this->assertTrue($this->policy->view($user, $like));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('view_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 2;

        $this->assertFalse($this->policy->view($user, $like));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like = Mockery::mock(Like::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $like));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_like')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('update_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 1;

        $this->assertTrue($this->policy->update($user, $like));
    }

    public function test_update_others_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('update_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 2;

        $this->assertFalse($this->policy->update($user, $like));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like = Mockery::mock(Like::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $like));
    }

    public function test_delete_own_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('delete_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $like));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('delete_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $like));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like = Mockery::mock(Like::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $like));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $like));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $like));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like = Mockery::mock(Like::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $like));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('restore_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $like));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('restore_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $like));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like = Mockery::mock(Like::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $like));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $like));
    }

    public function test_replicate_others_like(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_like')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like          = Mockery::mock(Like::class)->makePartial();
        $like->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $like));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_like')->andReturn(true);

        /** @var Like|MockInterface $like */
        $like = Mockery::mock(Like::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $like));
    }
}
