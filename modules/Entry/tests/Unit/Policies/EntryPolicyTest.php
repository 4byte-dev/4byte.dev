<?php

namespace Modules\Entry\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\Entry\Models\Entry;
use Modules\Entry\Policies\EntryPolicy;
use Modules\Entry\Tests\TestCase;

class EntryPolicyTest extends TestCase
{
    private EntryPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new EntryPolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_entry')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('view_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 1;

        $this->assertTrue($this->policy->view($user, $entry));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('view_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 2;

        $this->assertFalse($this->policy->view($user, $entry));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry = Mockery::mock(Entry::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $entry));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_entry')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('update_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 1;

        $this->assertTrue($this->policy->update($user, $entry));
    }

    public function test_update_others_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('update_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 2;

        $this->assertFalse($this->policy->update($user, $entry));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry = Mockery::mock(Entry::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $entry));
    }

    public function test_delete_own_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('delete_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $entry));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('delete_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $entry));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry = Mockery::mock(Entry::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $entry));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $entry));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $entry));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry = Mockery::mock(Entry::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $entry));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('restore_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $entry));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('restore_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $entry));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry = Mockery::mock(Entry::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $entry));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $entry));
    }

    public function test_replicate_others_entry(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_entry')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry          = Mockery::mock(Entry::class)->makePartial();
        $entry->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $entry));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_entry')->andReturn(true);

        /** @var Entry|MockInterface $entry */
        $entry = Mockery::mock(Entry::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $entry));
    }
}
