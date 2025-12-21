<?php

namespace Modules\Course\Filament\Resources\CourseResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Course\Filament\Resources\CourseResource;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;
}
