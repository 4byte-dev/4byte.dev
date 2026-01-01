<?php

namespace Modules\CourseLesson\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Policies\CourseLessonPolicy;
use Modules\Course\Tests\TestCase;

class CourseLessonPolicyTest extends TestCase
{
    private CourseLessonPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CourseLessonPolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_course::lesson')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('view_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertTrue($this->policy->view($user, $courseLesson));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('view_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertFalse($this->policy->view($user, $courseLesson));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson = Mockery::mock(CourseLesson::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $courseLesson));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_course::lesson')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('update_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertTrue($this->policy->update($user, $courseLesson));
    }

    public function test_update_others_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('update_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertFalse($this->policy->update($user, $courseLesson));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson = Mockery::mock(CourseLesson::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $courseLesson));
    }

    public function test_delete_own_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('delete_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertTrue($this->policy->delete($user, $courseLesson));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('delete_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertFalse($this->policy->delete($user, $courseLesson));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson = Mockery::mock(CourseLesson::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $courseLesson));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertTrue($this->policy->forceDelete($user, $courseLesson));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertFalse($this->policy->forceDelete($user, $courseLesson));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson = Mockery::mock(CourseLesson::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $courseLesson));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('restore_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertTrue($this->policy->restore($user, $courseLesson));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('restore_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertFalse($this->policy->restore($user, $courseLesson));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson = Mockery::mock(CourseLesson::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $courseLesson));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 1;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertTrue($this->policy->replicate($user, $courseLesson));
    }

    public function test_replicate_others_courseLesson(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_course::lesson')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson          = Mockery::mock(CourseLesson::class)->makePartial();
        /** @var CourseChapter|MockInterface $chapter */
        $chapter = Mockery::mock(CourseChapter::class)->makePartial();
        /** @var Course|MockInterface $course */
        $course          = Mockery::mock(Course::class)->makePartial();
        $course->user_id = 2;
        $chapter->setRelation('course', $course);
        $courseLesson->setRelation('chapter', $chapter);

        $this->assertFalse($this->policy->replicate($user, $courseLesson));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_course::lesson')->andReturn(true);

        /** @var CourseLesson|MockInterface $courseLesson */
        $courseLesson = Mockery::mock(CourseLesson::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $courseLesson));
    }
}
