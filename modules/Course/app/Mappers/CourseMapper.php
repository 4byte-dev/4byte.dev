<?php

namespace Modules\Course\Mappers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Category\Mappers\CategoryMapper;
use Modules\Course\Data\CourseData;
use Modules\Course\Data\CourseLessonData;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseLesson;
use Modules\Tag\Mappers\TagMapper;
use Modules\User\Data\UserData;

class CourseMapper
{
    /**
     * Create a CourseData instance from a Course model.
     */
    public static function toData(Course $course, UserData $user, bool $setId = false): CourseData
    {
        $userId = Auth::id();

        return new CourseData(
            id: $setId ? $course->id : 0,
            title: $course->title,
            slug: $course->slug,
            difficulty: $course->difficulty,
            content: $course->content,
            excerpt: $course->excerpt,
            image: $course->getCoverImage(),
            published_at: $course->published_at,
            user: $user,
            categories: CategoryMapper::collection($course->categories),
            tags: TagMapper::collection($course->tags),
            likes: $course->likesCount(),
            dislikes: $course->dislikesCount(),
            comments: $course->commentsCount(),
            isLiked: $course->isLikedBy($userId),
            isDisliked: $course->isDislikedBy($userId),
            isSaved: $course->isSavedBy($userId),
            canUpdate: Gate::allows('update', $course),
            canDelete: Gate::allows('delete', $course)
        );
    }

    /**
     * Create a CourseLessonData instance from a CourseLesson model.
     */
    public static function toLessonData(CourseLesson $lesson, bool $setId = false): CourseLessonData
    {
        $userId = Auth::id();

        return new CourseLessonData(
            id: $setId ? $lesson->id : 0,
            title: $lesson->title,
            slug: $lesson->slug,
            content: $lesson->content,
            video_url: $lesson->video_url,
            published_at: $lesson->published_at,
            isSaved: $lesson->isSavedBy($userId),
            canUpdate: Gate::allows('update', $lesson),
            canDelete: Gate::allows('delete', $lesson)
        );
    }
}
