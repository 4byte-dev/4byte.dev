<?php

namespace Modules\React\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\React\Models\Save;
use Modules\React\Policies\SavePolicy;
use Modules\React\Tests\TestCase;

class SavePolicyTest extends TestCase
{
    private SavePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new SavePolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_save')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('view_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 1;

        $this->assertTrue($this->policy->view($user, $save));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('view_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 2;

        $this->assertFalse($this->policy->view($user, $save));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save = Mockery::mock(Save::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $save));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_save')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('update_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 1;

        $this->assertTrue($this->policy->update($user, $save));
    }

    public function test_update_others_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('update_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 2;

        $this->assertFalse($this->policy->update($user, $save));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save = Mockery::mock(Save::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $save));
    }

    public function test_delete_own_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('delete_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $save));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('delete_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $save));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save = Mockery::mock(Save::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $save));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $save));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $save));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save = Mockery::mock(Save::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $save));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('restore_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $save));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('restore_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $save));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save = Mockery::mock(Save::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $save));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $save));
    }

    public function test_replicate_others_save(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_save')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save          = Mockery::mock(Save::class)->makePartial();
        $save->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $save));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_save')->andReturn(true);

        /** @var Save|MockInterface $save */
        $save = Mockery::mock(Save::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $save));
    }
}
