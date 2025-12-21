<?php

namespace Modules\Course\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Course\Models\CourseChapter;

class CourseChapterObserver
{
    /**
     * Handle the "saved" event for the CourseChapter model.
     */
    public function saved(CourseChapter $courseChapter): void
    {
        $this->clearCourseCache($courseChapter);
    }

    /**
     * Handle the "deleted" event for the CourseChapter model.
     */
    public function deleted(CourseChapter $courseChapter): void
    {
        $this->clearCourseCache($courseChapter);
    }

    protected function clearCourseCache(CourseChapter $courseChapter): void
    {
        if ($courseChapter->course_id) {
            Cache::forget("course:{$courseChapter->course_id}");
            Cache::forget("course:{$courseChapter->course_id}:cirriculum");
        }
    }
}
