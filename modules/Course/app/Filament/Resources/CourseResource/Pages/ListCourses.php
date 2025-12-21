<?php

namespace Modules\Course\Filament\Resources\CourseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Course\Filament\Resources\CourseResource;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
