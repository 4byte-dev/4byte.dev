<?php

namespace Modules\Course\Tests\Unit\Data;

use Illuminate\Support\Carbon;
use Mockery;
use Mockery\MockInterface;
use Modules\Course\Data\CourseLessonData;
use Modules\Course\Mappers\CourseMapper;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Tests\TestCase;
use Modules\User\Models\User;

class CourseLessonDataTest extends TestCase
{
    public function test_it_creates_from_model(): void
    {
        $lessonData = new CourseLessonData(
            id: 2,
            title: "Test Lesson",
            slug: "test-slug",
            content: "Test Content",
            video_url: "https://4byte.dev/video",
            published_at: now(),
            isSaved: false,
            canUpdate: false,
            canDelete: false
        );

        $this->assertSame(2, $lessonData->id);
        $this->assertSame('Test Lesson', $lessonData->title);
        $this->assertSame('test-slug', $lessonData->slug);
        $this->assertSame('Test Content', $lessonData->content);
        $this->assertSame('https://4byte.dev/video', $lessonData->video_url);

        $this->assertFalse($lessonData->isSaved);

        $this->assertFalse($lessonData->canUpdate);
        $this->assertFalse($lessonData->canDelete);

        $this->assertInstanceOf(Carbon::class, $lessonData->published_at);

        $this->assertSame('lesson', $lessonData->type);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $lesson = CourseLesson::factory()->create([
            'title'   => 'Test Lesson',
            'slug'    => 'test-slug',
            'content' => 'Test Content',
        ]);

        $lessonData = CourseMapper::toLessonData($lesson);

        $this->assertSame(0, $lessonData->id);

        $this->assertSame('Test Lesson', $lessonData->title);
        $this->assertSame('test-slug', $lessonData->slug);
        $this->assertSame('Test Content', $lessonData->content);

        $this->assertFalse($lessonData->isSaved);

        $this->assertFalse($lessonData->canUpdate);
        $this->assertFalse($lessonData->canDelete);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $lesson = CourseLesson::factory()->create();

        $lessonData = CourseMapper::toLessonData($lesson, true);

        $this->assertSame($lesson->id, $lessonData->id);
    }

    public function test_it_uses_model_methods_for_followers_and_follow_state(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var CourseLesson|MockInterface $lesson */
        $lesson        = Mockery::mock(CourseLesson::class)->makePartial();
        $lesson->id    = 10;
        $lesson->title = 'Test Lesson';
        $lesson->slug  = 'test-lesson';

        $lesson->shouldReceive('isSavedBy')
            ->once()
            ->with($user->id)
            ->andReturn(true);

        $data = CourseMapper::toLessonData($lesson, true);

        $this->assertSame(10, $data->id);
        $this->assertTrue($data->isSaved);
    }

    public function test_it_sets_like_and_dislike_state_as_false_for_guest_user(): void
    {
        $user = User::factory()->create();

        /** @var CourseLesson|MockInterface $lesson */
        $lesson        = Mockery::mock(CourseLesson::class)->makePartial();
        $lesson->id    = 1;
        $lesson->title = 'Guest Lesson';
        $lesson->slug  = 'guest-lesson';

        $lesson->shouldReceive('isSavedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $data = CourseMapper::toLessonData($lesson);

        $this->assertFalse($data->isSaved);
    }
}
