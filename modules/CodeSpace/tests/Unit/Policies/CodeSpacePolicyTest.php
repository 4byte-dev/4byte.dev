<?php

namespace Modules\CodeSpace\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Policies\CodeSpacePolicy;
use Modules\CodeSpace\Tests\TestCase;

class CodeSpacePolicyTest extends TestCase
{
    private CodeSpacePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CodeSpacePolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_code::space')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('view_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 1;

        $this->assertTrue($this->policy->view($user, $codeSpace));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('view_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 2;

        $this->assertFalse($this->policy->view($user, $codeSpace));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace = Mockery::mock(CodeSpace::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $codeSpace));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_code::space')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('update_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 1;

        $this->assertTrue($this->policy->update($user, $codeSpace));
    }

    public function test_update_others_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('update_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 2;

        $this->assertFalse($this->policy->update($user, $codeSpace));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace = Mockery::mock(CodeSpace::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $codeSpace));
    }

    public function test_delete_own_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('delete_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $codeSpace));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('delete_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $codeSpace));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace = Mockery::mock(CodeSpace::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $codeSpace));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $codeSpace));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $codeSpace));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace = Mockery::mock(CodeSpace::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $codeSpace));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('restore_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $codeSpace));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('restore_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $codeSpace));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace = Mockery::mock(CodeSpace::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $codeSpace));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $codeSpace));
    }

    public function test_replicate_others_codeSpace(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_code::space')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace          = Mockery::mock(CodeSpace::class)->makePartial();
        $codeSpace->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $codeSpace));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_code::space')->andReturn(true);

        /** @var CodeSpace|MockInterface $codeSpace */
        $codeSpace = Mockery::mock(CodeSpace::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $codeSpace));
    }
}
