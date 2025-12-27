<?php

namespace Modules\Course\Filament\Resources\CourseLessonResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Course\Filament\Resources\CourseLessonResource;

class CreateCourseLesson extends CreateRecord
{
    protected static string $resource = CourseLessonResource::class;
}
