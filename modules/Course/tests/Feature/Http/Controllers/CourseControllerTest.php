<?php

namespace Modules\Course\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Course\Data\CourseData;
use Modules\Course\Data\CourseLessonData;
use Modules\Course\Services\CourseService;
use Modules\Course\Tests\TestCase;
use Modules\User\Data\UserData;

class CourseControllerTest extends TestCase
{
    public function test_it_displays_course_detail_page(): void
    {
        $courseId = 1;
        $slug     = 'test-course';

        $userData = new UserData(
            id: 1,
            name: 'User',
            username: 'user',
            avatar: '',
            followers: 0,
            followings: 0,
            isFollowing: false,
            created_at: now()
        );

        $courseData = new CourseData(
            id: $courseId,
            title: 'Test Course',
            slug: $slug,
            difficulty: 'beginner',
            excerpt: 'Excerpt',
            content: 'Content',
            image: [
                'image'      => 'https://cdn.4byte.dev/logo.png',
                'responsive' => [],
                'srcset'     => '',
                'thumb'      => null,
            ],
            published_at: now(),
            user: $userData,
            categories: [],
            tags: [],
            likes: 10,
            dislikes: 0,
            comments: 5,
            isLiked: false,
            isDisliked: false,
            isSaved: false,
            canUpdate: false,
            canDelete: false
        );

        $cirriculum = [
            [
                'id'        => 1,
                'title'     => 'Chapter 1',
                'course_id' => $courseId,
                'lessons'   => [
                    [
                        'id'         => 1,
                        'title'      => 'Lesson 1',
                        'slug'       => 'lesson-1',
                        'chapter_id' => 1,
                    ],
                ],
            ],
        ];

        $courseService = Mockery::mock(CourseService::class);
        $courseService->shouldReceive('getId')->with($slug)->once()->andReturn($courseId);
        $courseService->shouldReceive('getData')->with($courseId)->once()->andReturn($courseData);
        $courseService->shouldReceive('getCirriculum')->with($courseId)->once()->andReturn($cirriculum);

        $this->app->instance(CourseService::class, $courseService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getCourseSEO')->with($courseData, $courseData->user)->once()->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('course.view', $slug));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Course/Detail')
                ->has('course')
                ->where('course.id', $courseId)
                ->where('course.title', 'Test Course')
                ->where('course.slug', $slug)
                ->where('course.difficulty', 'beginner')
                ->where('course.excerpt', 'Excerpt')
                ->where('course.content', 'Content')

                ->has('cirriculum', 1)
                ->where('cirriculum.0.title', 'Chapter 1')

                ->has('cirriculum.0.lessons', 1)
                ->where('cirriculum.0.lessons.0.title', 'Lesson 1')
                ->where('cirriculum.0.lessons.0.slug', 'lesson-1')
        );
    }

    public function test_it_displays_lesson_page(): void
    {
        $courseId   = 1;
        $courseSlug = 'test-course';
        $lessonId   = 10;
        $lessonPage = 1;

        $lessonData = new CourseLessonData(
            id: $lessonId,
            title: 'Lesson 1',
            slug: 'lesson-1',
            content: 'Content',
            video_url: null,
            published_at: now(),
            isSaved: false,
            canUpdate: false,
            canDelete: false
        );

        $cirriculum = [];

        $courseService = Mockery::mock(CourseService::class);
        $courseService->shouldReceive('getId')->with($courseSlug)->once()->andReturn($courseId);
        $courseService->shouldReceive('getLessonId')->with($courseId, $lessonPage)->once()->andReturn($lessonId);
        $courseService->shouldReceive('getLesson')->with($courseId, $lessonId)->once()->andReturn($lessonData);
        $courseService->shouldReceive('getCirriculum')->with($courseId)->once()->andReturn($cirriculum);

        $this->app->instance(CourseService::class, $courseService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getCourseLessonSEO')->with($lessonData, $courseSlug)->once()->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('course.page', ['slug' => $courseSlug, 'page' => $lessonPage]));

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Course/Page')
                ->where('course', $courseSlug)

                ->has('lesson')
                ->where('lesson.title', 'Lesson 1')
                ->where('lesson.slug', 'lesson-1')
                ->where('lesson.content', 'Content')
        );
    }
}
