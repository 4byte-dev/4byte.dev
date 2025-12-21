<?php

namespace Modules\Course\Filament\Resources\CourseChapterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Course\Filament\Resources\CourseChapterResource;

class EditCourseChapter extends EditRecord
{
    protected static string $resource = CourseChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
