<?php

namespace Packages\Category\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Packages\Category\Policies\CategoryPolicy;
use Packages\Category\Tests\TestCase;

class CategoryPolicyTest extends TestCase
{
    protected CategoryPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CategoryPolicy();
    }

    public function test_view(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('view_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('view_category')->andReturn(true);
        $this->assertTrue($this->policy->view($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('view_any_category')->andReturn(true);
        $this->assertTrue($this->policy->view($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('view_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('view_category')->andReturn(false);
        $this->assertFalse($this->policy->view($user));
    }

    public function test_create(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('create_category')->andReturn(true);
        $this->assertTrue($this->policy->create($user));
    }

    public function test_update(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('update_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('update_category')->andReturn(true);
        $this->assertTrue($this->policy->update($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('update_any_category')->andReturn(true);
        $this->assertTrue($this->policy->update($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('update_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('update_category')->andReturn(false);
        $this->assertFalse($this->policy->update($user));
    }

    public function test_delete(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('delete_category')->andReturn(true);
        $this->assertTrue($this->policy->delete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_category')->andReturn(true);
        $this->assertTrue($this->policy->delete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('delete_category')->andReturn(false);
        $this->assertFalse($this->policy->delete($user));
    }

    public function test_delete_any(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_category')->andReturn(true);
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_category')->andReturn(true);
        $this->assertTrue($this->policy->forceDelete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_category')->andReturn(true);
        $this->assertTrue($this->policy->forceDelete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_category')->andReturn(false);
        $this->assertFalse($this->policy->forceDelete($user));
    }

    public function test_force_delete_any(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_category')->andReturn(true);
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('restore_category')->andReturn(true);
        $this->assertTrue($this->policy->restore($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_category')->andReturn(true);
        $this->assertTrue($this->policy->restore($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('restore_category')->andReturn(false);
        $this->assertFalse($this->policy->restore($user));
    }

    public function test_restore_any(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_category')->andReturn(true);
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('replicate_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_category')->andReturn(true);
        $this->assertTrue($this->policy->replicate($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('replicate_any_category')->andReturn(true);
        $this->assertTrue($this->policy->replicate($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('replicate_any_category')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_category')->andReturn(false);
        $this->assertFalse($this->policy->replicate($user));
    }

    public function test_reorder(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('reorder_category')->andReturn(true);
        $this->assertTrue($this->policy->reorder($user));
    }
}
