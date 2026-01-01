<?php

namespace Modules\Course\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Services\CourseService;
use Modules\Course\Tests\TestCase;

class CourseServiceTest extends TestCase
{
    protected CourseService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(CourseService::class);
    }

    public function test_it_returns_course_data(): void
    {
        $course = Course::factory()->create([
            'status' => 'PUBLISHED',
        ]);

        $data = $this->service->getData($course->id);

        $this->assertEquals($course->title, $data->title);
        $this->assertEquals($course->slug, $data->slug);
        $this->assertEquals($course->excerpt, $data->excerpt);

        $this->assertTrue(Cache::has("course:{$course->id}"));
    }

    public function test_it_can_get_course_id_by_slug(): void
    {
        $course = Course::factory()->create([
            'status' => 'PUBLISHED',
        ]);

        $id = $this->service->getId($course->slug);

        $this->assertEquals($course->id, $id);
        $this->assertTrue(Cache::has("course:{$course->slug}:id"));
    }

    public function test_it_returns_course_cirriculum(): void
    {
        $course = Course::factory()->create();

        $chapter = CourseChapter::factory()->create([
            'course_id' => $course->id,
        ]);

        CourseLesson::factory()->create([
            'chapter_id' => $chapter->id,
        ]);

        $cirriculum = $this->service->getCirriculum($course->id);

        $this->assertCount(1, $cirriculum);
        $this->assertEquals($chapter->id, $cirriculum[0]['id']);
        $this->assertArrayHasKey('lessons', $cirriculum[0]);

        $this->assertTrue(Cache::has("course:{$course->id}:cirriculum"));
    }

    public function test_it_can_get_lesson_id_by_slug(): void
    {
        $course = Course::factory()->create();

        $chapter = CourseChapter::factory()->create([
            'course_id' => $course->id,
        ]);

        $lesson = CourseLesson::factory()->create([
            'chapter_id' => $chapter->id,
        ]);

        $lessonId = $this->service->getLessonId($course->id, $lesson->slug);

        $this->assertEquals($lesson->id, $lessonId);
        $this->assertTrue(
            Cache::has("course:{$course->id}:lesson:{$lesson->slug}:id")
        );
    }

    public function test_it_returns_lesson_data_by_course_and_lesson_id(): void
    {
        $course = Course::factory()->create();

        $chapter = CourseChapter::factory()->create([
            'course_id' => $course->id,
        ]);

        $lesson = CourseLesson::factory()->create([
            'chapter_id' => $chapter->id,
            'status'     => 'PUBLISHED',
        ]);

        $data = $this->service->getLesson($course->id, $lesson->id);

        $this->assertEquals($lesson->title, $data->title);
        $this->assertEquals($lesson->slug, $data->slug);
        $this->assertEquals($lesson->content, $data->content);

        $this->assertTrue(
            Cache::has("course:{$course->id}:lesson:{$lesson->id}")
        );
    }

    public function test_it_returns_lesson_data_by_chapter(): void
    {
        $chapter = CourseChapter::factory()->create();

        $lesson = CourseLesson::factory()->create([
            'chapter_id' => $chapter->id,
            'status'     => 'PUBLISHED',
        ]);

        $data = $this->service->getLessonByChapter($chapter->id, $lesson->id);

        $this->assertEquals($lesson->title, $data->title);
        $this->assertEquals($lesson->slug, $data->slug);
        $this->assertEquals($lesson->content, $data->content);

        $this->assertTrue(
            Cache::has("course:chapter:{$chapter->id}:lesson:{$lesson->id}")
        );
    }
}
