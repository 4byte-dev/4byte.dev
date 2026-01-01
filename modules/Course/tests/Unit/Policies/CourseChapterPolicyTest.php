<?php

namespace Modules\CourseChapter\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Policies\CourseChapterPolicy;
use Modules\Course\Tests\TestCase;

class CourseChapterPolicyTest extends TestCase
{
    private CourseChapterPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CourseChapterPolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_course::chapter')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('view_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $courseChapter->setRelation('course', $course);

        $this->assertTrue($this->policy->view($user, $courseChapter));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('view_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $courseChapter->setRelation('course', $course);

        $this->assertFalse($this->policy->view($user, $courseChapter));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter = Mockery::mock(CourseChapter::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $courseChapter));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_course::chapter')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('update_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $courseChapter->setRelation('course', $course);

        $this->assertTrue($this->policy->update($user, $courseChapter));
    }

    public function test_update_others_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('update_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $courseChapter->setRelation('course', $course);

        $this->assertFalse($this->policy->update($user, $courseChapter));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter = Mockery::mock(CourseChapter::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $courseChapter));
    }

    public function test_delete_own_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('delete_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $courseChapter->setRelation('course', $course);

        $this->assertTrue($this->policy->delete($user, $courseChapter));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('delete_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $courseChapter->setRelation('course', $course);

        $this->assertFalse($this->policy->delete($user, $courseChapter));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter = Mockery::mock(CourseChapter::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $courseChapter));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $courseChapter->setRelation('course', $course);

        $this->assertTrue($this->policy->forceDelete($user, $courseChapter));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $courseChapter->setRelation('course', $course);

        $this->assertFalse($this->policy->forceDelete($user, $courseChapter));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter = Mockery::mock(CourseChapter::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $courseChapter));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('restore_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $courseChapter->setRelation('course', $course);

        $this->assertTrue($this->policy->restore($user, $courseChapter));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('restore_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $courseChapter->setRelation('course', $course);

        $this->assertFalse($this->policy->restore($user, $courseChapter));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter = Mockery::mock(CourseChapter::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $courseChapter));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $courseChapter->setRelation('course', $course);

        $this->assertTrue($this->policy->replicate($user, $courseChapter));
    }

    public function test_replicate_others_courseChapter(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_course::chapter')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter          = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $courseChapter->setRelation('course', $course);

        $this->assertFalse($this->policy->replicate($user, $courseChapter));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_course::chapter')->andReturn(true);

        /** @var CourseChapter|MockInterface $courseChapter */
        $courseChapter = Mockery::mock(CourseChapter::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $courseChapter));
    }
}
