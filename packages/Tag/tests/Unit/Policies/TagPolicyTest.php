<?php

namespace Packages\Tag\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Packages\Tag\Policies\TagPolicy;
use Packages\Tag\Tests\TestCase;

class TagPolicyTest extends TestCase
{
    protected TagPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new TagPolicy();
    }

    public function test_view(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('view_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('view_tag')->andReturn(true);
        $this->assertTrue($this->policy->view($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('view_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->view($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('view_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('view_tag')->andReturn(false);
        $this->assertFalse($this->policy->view($user));
    }

    public function test_create(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('create_tag')->andReturn(true);
        $this->assertTrue($this->policy->create($user));
    }

    public function test_update(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('update_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('update_tag')->andReturn(true);
        $this->assertTrue($this->policy->update($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('update_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->update($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('update_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('update_tag')->andReturn(false);
        $this->assertFalse($this->policy->update($user));
    }

    public function test_delete(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('delete_tag')->andReturn(true);
        $this->assertTrue($this->policy->delete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->delete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('delete_tag')->andReturn(false);
        $this->assertFalse($this->policy->delete($user));
    }

    public function test_delete_any(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('delete_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_tag')->andReturn(true);
        $this->assertTrue($this->policy->forceDelete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->forceDelete($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_tag')->andReturn(false);
        $this->assertFalse($this->policy->forceDelete($user));
    }

    public function test_force_delete_any(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('force_delete_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('restore_tag')->andReturn(true);
        $this->assertTrue($this->policy->restore($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->restore($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('restore_tag')->andReturn(false);
        $this->assertFalse($this->policy->restore($user));
    }

    public function test_restore_any(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('restore_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('replicate_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_tag')->andReturn(true);
        $this->assertTrue($this->policy->replicate($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('replicate_any_tag')->andReturn(true);
        $this->assertTrue($this->policy->replicate($user));

        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('replicate_any_tag')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_tag')->andReturn(false);
        $this->assertFalse($this->policy->replicate($user));
    }

    public function test_reorder(): void
    {
        $user = Mockery::mock(User::class);
        $user->shouldReceive('can')->with('reorder_tag')->andReturn(true);
        $this->assertTrue($this->policy->reorder($user));
    }
}
