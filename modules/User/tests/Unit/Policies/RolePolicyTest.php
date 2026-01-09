<?php

namespace Modules\User\Tests\Unit\Policies;

use Mockery;
use Mockery\MockInterface;
use Modules\User\Models\User;
use Modules\User\Policies\RolePolicy;
use Modules\User\Tests\TestCase;

class RolePolicyTest extends TestCase
{
    private RolePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new RolePolicy();
    }

    public function test_view_any_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('view_any_role')->andReturn(true);

        $this->assertTrue($this->policy->viewAny($role));
    }

    public function test_view_any_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('view_any_role')->andReturn(false);

        $this->assertFalse($this->policy->viewAny($role));
    }

    public function test_view_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('view_role')->andReturn(true);

        $this->assertTrue($this->policy->view($role));
    }

    public function test_view_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('view_role')->andReturn(false);

        $this->assertFalse($this->policy->view($role));
    }

    public function test_create_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('create_role')->andReturn(true);

        $this->assertTrue($this->policy->create($role));
    }

    public function test_create_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('create_role')->andReturn(false);

        $this->assertFalse($this->policy->create($role));
    }

    public function test_update_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('update_role')->andReturn(true);

        $this->assertTrue($this->policy->update($role));
    }

    public function test_update_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('update_role')->andReturn(false);

        $this->assertFalse($this->policy->update($role));
    }

    public function test_delete_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('delete_role')->andReturn(true);

        $this->assertTrue($this->policy->delete($role));
    }

    public function test_delete_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('delete_role')->andReturn(false);

        $this->assertFalse($this->policy->delete($role));
    }

    public function test_delete_any_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('delete_any_role')->andReturn(true);

        $this->assertTrue($this->policy->deleteAny($role));
    }

    public function test_delete_any_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('delete_any_role')->andReturn(false);

        $this->assertFalse($this->policy->deleteAny($role));
    }

    public function test_force_delete_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('force_delete_role')->andReturn(true);

        $this->assertTrue($this->policy->forceDelete($role));
    }

    public function test_force_delete_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('force_delete_role')->andReturn(false);

        $this->assertFalse($this->policy->forceDelete($role));
    }

    public function test_force_delete_any_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('force_delete_any_role')->andReturn(true);

        $this->assertTrue($this->policy->forceDeleteAny($role));
    }

    public function test_force_delete_any_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('force_delete_any_role')->andReturn(false);

        $this->assertFalse($this->policy->forceDeleteAny($role));
    }

    public function test_restore_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('restore_role')->andReturn(true);

        $this->assertTrue($this->policy->restore($role));
    }

    public function test_restore_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('restore_role')->andReturn(false);

        $this->assertFalse($this->policy->restore($role));
    }

    public function test_restore_any_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('restore_any_role')->andReturn(true);

        $this->assertTrue($this->policy->restoreAny($role));
    }

    public function test_restore_any_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('restore_any_role')->andReturn(false);

        $this->assertFalse($this->policy->restoreAny($role));
    }

    public function test_replicate_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('replicate_role')->andReturn(true);

        $this->assertTrue($this->policy->replicate($role));
    }

    public function test_replicate_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('replicate_role')->andReturn(false);

        $this->assertFalse($this->policy->replicate($role));
    }

    public function test_reorder_allowed(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('reorder_role')->andReturn(true);

        $this->assertTrue($this->policy->reorder($role));
    }

    public function test_reorder_denied(): void
    {
        /** @var User|MockInterface $role */
        $role = Mockery::mock(User::class)->makePartial();
        $role->shouldReceive('can')->with('reorder_role')->andReturn(false);

        $this->assertFalse($this->policy->reorder($role));
    }
}
