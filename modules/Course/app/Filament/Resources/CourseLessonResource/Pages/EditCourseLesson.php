<?php

namespace Modules\Course\Filament\Resources\CourseLessonResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Course\Filament\Resources\CourseLessonResource;

class EditCourseLesson extends EditRecord
{
    protected static string $resource = CourseLessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
