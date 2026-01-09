<?php

namespace Modules\User\Tests\Unit\Policies;

use Mockery;
use Mockery\MockInterface;
use Modules\User\Models\User;
use Modules\User\Policies\UserPolicy;
use Modules\User\Tests\TestCase;

class UserPolicyTest extends TestCase
{
    private UserPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new UserPolicy();
    }

    public function test_view_any_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_user')->andReturn(true);

        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_any_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_user')->andReturn(false);

        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_user')->andReturn(true);

        $this->assertTrue($this->policy->view($user));
    }

    public function test_view_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_user')->andReturn(false);

        $this->assertFalse($this->policy->view($user));
    }

    public function test_create_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_user')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_create_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_user')->andReturn(false);

        $this->assertFalse($this->policy->create($user));
    }

    public function test_update_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_user')->andReturn(true);

        $this->assertTrue($this->policy->update($user));
    }

    public function test_update_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_user')->andReturn(false);

        $this->assertFalse($this->policy->update($user));
    }

    public function test_delete_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_user')->andReturn(true);

        $this->assertTrue($this->policy->delete($user));
    }

    public function test_delete_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_user')->andReturn(false);

        $this->assertFalse($this->policy->delete($user));
    }

    public function test_delete_any_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_user')->andReturn(true);

        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_delete_any_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_user')->andReturn(false);

        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_force_delete_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_user')->andReturn(true);

        $this->assertTrue($this->policy->forceDelete($user));
    }

    public function test_force_delete_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_user')->andReturn(false);

        $this->assertFalse($this->policy->forceDelete($user));
    }

    public function test_force_delete_any_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_user')->andReturn(true);

        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_user')->andReturn(false);

        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_restore_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_user')->andReturn(true);

        $this->assertTrue($this->policy->restore($user));
    }

    public function test_restore_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_user')->andReturn(false);

        $this->assertFalse($this->policy->restore($user));
    }

    public function test_restore_any_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_user')->andReturn(true);

        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_restore_any_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_user')->andReturn(false);

        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_replicate_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_user')->andReturn(true);

        $this->assertTrue($this->policy->replicate($user));
    }

    public function test_replicate_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_user')->andReturn(false);

        $this->assertFalse($this->policy->replicate($user));
    }

    public function test_reorder_allowed(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('reorder_user')->andReturn(true);

        $this->assertTrue($this->policy->reorder($user));
    }

    public function test_reorder_denied(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('reorder_user')->andReturn(false);

        $this->assertFalse($this->policy->reorder($user));
    }
}
