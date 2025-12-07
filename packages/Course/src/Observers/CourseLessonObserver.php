<?php

namespace Packages\Course\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Course\Models\CourseLesson;

class CourseLessonObserver
{
    /**
     * Handle the "saved" event for the CourseLesson model.
     */
    public function saved(CourseLesson $courseLesson): void
    {
        $this->clearCourseCache($courseLesson);
    }

    /**
     * Handle the "deleted" event for the CourseLesson model.
     */
    public function deleted(CourseLesson $courseLesson): void
    {
        $this->clearCourseCache($courseLesson);
    }

    protected function clearCourseCache(CourseLesson $courseLesson): void
    {
        $courseId = $courseLesson->chapter->course_id;
        Cache::forget("course:{$courseId}");
        Cache::forget("course:{$courseId}:cirriculum");
        Cache::forget("course:{$courseId}:lesson:{$courseLesson->id}");
        Cache::forget("course:{$courseId}:lesson:{$courseLesson->slug}:id");
    }
}
