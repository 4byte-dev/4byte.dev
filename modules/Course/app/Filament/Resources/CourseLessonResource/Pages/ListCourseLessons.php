<?php

namespace Modules\Course\Filament\Resources\CourseLessonResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Course\Filament\Resources\CourseLessonResource;

class ListCourseLessons extends ListRecords
{
    protected static string $resource = CourseLessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
