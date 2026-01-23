<?php

namespace Modules\Course\Tests\Unit\Data;

use Illuminate\Support\Carbon;
use Mockery;
use Mockery\MockInterface;
use Modules\Category\Data\CategoryData;
use Modules\Course\Data\CourseData;
use Modules\Course\Mappers\CourseMapper;
use Modules\Course\Models\Course;
use Modules\Course\Tests\TestCase;
use Modules\Tag\Data\TagData;
use Modules\User\Data\UserData;
use Modules\User\Mappers\UserMapper;
use Modules\User\Models\User;

class CourseDataTest extends TestCase
{
    public function test_it_creates_from_model(): void
    {
        $userData = new UserData(
            id: 10,
            name: 'Test User',
            username: 'testuser',
            avatar: '',
            followers: 10,
            followings: 3,
            isFollowing: true,
            created_at: now()
        );

        $courseData = new CourseData(
            id: 2,
            title: 'Course Title',
            slug: 'course-slug',
            difficulty: 'beginner',
            excerpt: 'Course Excerpt',
            content: 'Course Content',
            image: [
                'image'      => 'https://cdn.4byte.dev/logo.png',
                'responsive' => [],
                'srcset'     => '',
                'thumb'      => null,
            ],
            published_at: now(),
            user: $userData,
            categories: [
                new CategoryData(
                    id: 3,
                    name: 'Category Test',
                    slug: 'category-test',
                    followers: 10,
                    isFollowing: true
                ),
            ],
            tags: [
                new TagData(
                    id: 2,
                    name: 'Tag Test',
                    slug: 'tag-test',
                    followers: 3,
                    isFollowing: false
                ),
            ],
            likes: 3,
            dislikes: 5,
            comments: 7,
            isLiked: false,
            isDisliked: true,
            isSaved: true,
            canUpdate: false,
            canDelete: false
        );

        $this->assertSame(2, $courseData->id);
        $this->assertSame('Course Title', $courseData->title);
        $this->assertSame('course-slug', $courseData->slug);
        $this->assertSame('beginner', $courseData->difficulty);
        $this->assertSame('Course Excerpt', $courseData->excerpt);
        $this->assertSame('Course Content', $courseData->content);

        $this->assertSame('https://cdn.4byte.dev/logo.png', $courseData->image['image']);

        $this->assertSame(3, $courseData->likes);
        $this->assertSame(5, $courseData->dislikes);
        $this->assertSame(7, $courseData->comments);

        $this->assertFalse($courseData->isLiked);
        $this->assertTrue($courseData->isDisliked);
        $this->assertTrue($courseData->isSaved);

        $this->assertFalse($courseData->canUpdate);
        $this->assertFalse($courseData->canDelete);

        $this->assertInstanceOf(Carbon::class, $courseData->published_at);
        $this->assertInstanceOf(UserData::class, $courseData->user);

        $this->assertSame(10, $courseData->user->id);
        $this->assertSame('Test User', $courseData->user->name);
        $this->assertSame('testuser', $courseData->user->username);
        $this->assertSame('', $courseData->user->avatar);

        $this->assertSame(10, $courseData->user->followers);
        $this->assertSame(3, $courseData->user->followings);
        $this->assertTrue($courseData->user->isFollowing);

        $this->assertInstanceOf(Carbon::class, $courseData->user->created_at);

        $this->assertCount(1, $courseData->categories);
        $this->assertSame('Category Test', $courseData->categories[0]->name);
        $this->assertSame('category-test', $courseData->categories[0]->slug);

        $this->assertCount(1, $courseData->tags);
        $this->assertSame('Tag Test', $courseData->tags[0]->name);
        $this->assertSame('tag-test', $courseData->tags[0]->slug);

        $this->assertSame('course', $courseData->type);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $course = Course::factory()->create([
            'title'   => 'Test Course',
            'slug'    => 'test-slug',
            'excerpt' => 'Test Excerpt',
            'content' => 'Test Content',
        ]);

        $user = User::factory()->create([
            'name'     => 'User Name',
            'username' => 'username',
        ]);

        $user = UserMapper::toData($user);

        $courseData = CourseMapper::toData($course, $user);

        $this->assertSame(0, $courseData->id);

        $this->assertSame('Test Course', $courseData->title);
        $this->assertSame('test-slug', $courseData->slug);
        $this->assertSame('Test Excerpt', $courseData->excerpt);
        $this->assertSame('Test Content', $courseData->content);

        $this->assertInstanceOf(UserData::class, $courseData->user);
        $this->assertSame($user->id, $courseData->user->id);
        $this->assertSame('User Name', $courseData->user->name);
        $this->assertSame('username', $courseData->user->username);

        $this->assertSame(0, $courseData->likes);
        $this->assertSame(0, $courseData->dislikes);
        $this->assertSame(0, $courseData->comments);

        $this->assertFalse($courseData->isLiked);
        $this->assertFalse($courseData->isDisliked);
        $this->assertFalse($courseData->isSaved);

        $this->assertFalse($courseData->canUpdate);
        $this->assertFalse($courseData->canDelete);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $course = Course::factory()->create();

        $user = User::factory()->create();

        $user = UserMapper::toData($user);

        $courseData = CourseMapper::toData($course, $user, true);

        $this->assertSame($course->id, $courseData->id);
    }

    public function test_it_uses_model_methods_for_followers_and_follow_state(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $userData = UserMapper::toData($user);

        /** @var Course|MockInterface $course */
        $course             = Mockery::mock(Course::class)->makePartial();
        $course->id         = 10;
        $course->title      = 'Test Course';
        $course->slug       = 'test-course';
        $course->difficulty = 'easy';

        $course->setRelation('categories', collect());
        $course->setRelation('tags', collect());

        $course->shouldReceive('likesCount')
            ->once()
            ->andReturn(15);

        $course->shouldReceive('dislikesCount')
            ->once()
            ->andReturn(3);

        $course->shouldReceive('isLikedBy')
            ->once()
            ->with($user->id)
            ->andReturn(true);

        $course->shouldReceive('isDislikedBy')
            ->once()
            ->with($user->id)
            ->andReturn(false);

        $data = CourseMapper::toData($course, $userData, true);

        $this->assertSame(10, $data->id);
        $this->assertSame(15, $data->likes);
        $this->assertSame(3, $data->dislikes);
        $this->assertTrue($data->isLiked);
        $this->assertFalse($data->isDisliked);
    }

    public function test_it_sets_like_and_dislike_state_as_false_for_guest_user(): void
    {
        $user = User::factory()->create();

        $userData = UserMapper::toData($user);

        /** @var Course|MockInterface $course */
        $course             = Mockery::mock(Course::class)->makePartial();
        $course->id         = 1;
        $course->title      = 'Guest Course';
        $course->slug       = 'guest-course';
        $course->difficulty = 'easy';

        $course->setRelation('categories', collect());
        $course->setRelation('tags', collect());

        $course->shouldReceive('likesCount')
            ->once()
            ->andReturn(0);

        $course->shouldReceive('dislikesCount')
            ->once()
            ->andReturn(0);

        $course->shouldReceive('isLikedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $course->shouldReceive('isDislikedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $data = CourseMapper::toData($course, $userData, true);

        $this->assertFalse($data->isLiked);
        $this->assertFalse($data->isDisliked);
    }
}
