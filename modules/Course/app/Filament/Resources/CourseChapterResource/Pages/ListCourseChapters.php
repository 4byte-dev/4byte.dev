<?php

namespace Modules\Course\Filament\Resources\CourseChapterResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Course\Filament\Resources\CourseChapterResource;

class ListCourseChapters extends ListRecords
{
    protected static string $resource = CourseChapterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
