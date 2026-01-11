<?php

namespace Modules\React\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\React\Models\Dislike;
use Modules\React\Policies\DislikePolicy;
use Modules\React\Tests\TestCase;

class DislikePolicyTest extends TestCase
{
    private DislikePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new DislikePolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_dislike')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('view_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 1;

        $this->assertTrue($this->policy->view($user, $dislike));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('view_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 2;

        $this->assertFalse($this->policy->view($user, $dislike));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike = Mockery::mock(Dislike::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $dislike));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_dislike')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('update_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 1;

        $this->assertTrue($this->policy->update($user, $dislike));
    }

    public function test_update_others_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('update_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 2;

        $this->assertFalse($this->policy->update($user, $dislike));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike = Mockery::mock(Dislike::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $dislike));
    }

    public function test_delete_own_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('delete_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $dislike));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('delete_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $dislike));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike = Mockery::mock(Dislike::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $dislike));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $dislike));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $dislike));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike = Mockery::mock(Dislike::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $dislike));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('restore_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $dislike));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('restore_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $dislike));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike = Mockery::mock(Dislike::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $dislike));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $dislike));
    }

    public function test_replicate_others_dislike(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_dislike')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike          = Mockery::mock(Dislike::class)->makePartial();
        $dislike->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $dislike));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_dislike')->andReturn(true);

        /** @var Dislike|MockInterface $dislike */
        $dislike = Mockery::mock(Dislike::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $dislike));
    }
}
