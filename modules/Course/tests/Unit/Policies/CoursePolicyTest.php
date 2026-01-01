<?php

namespace Modules\Course\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\Course\Models\Course;
use Modules\Course\Policies\CoursePolicy;
use Modules\Course\Tests\TestCase;

class CoursePolicyTest extends TestCase
{
    private CoursePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CoursePolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_course')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('view_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;

        $this->assertTrue($this->policy->view($user, $course));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('view_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;

        $this->assertFalse($this->policy->view($user, $course));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course = Mockery::mock(Course::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $course));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_course')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('update_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;

        $this->assertTrue($this->policy->update($user, $course));
    }

    public function test_update_others_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('update_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;

        $this->assertFalse($this->policy->update($user, $course));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course = Mockery::mock(Course::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $course));
    }

    public function test_delete_own_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('delete_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $course));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('delete_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $course));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course = Mockery::mock(Course::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $course));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $course));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $course));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course = Mockery::mock(Course::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $course));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('restore_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $course));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('restore_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $course));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course = Mockery::mock(Course::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $course));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $course));
    }

    public function test_replicate_others_course(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_course')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $course));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_course')->andReturn(true);

        /** @var Course|MockInterface $course */
        $course = Mockery::mock(Course::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $course));
    }
}
