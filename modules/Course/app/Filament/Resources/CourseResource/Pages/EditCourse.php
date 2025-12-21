<?php

namespace Modules\Course\Filament\Resources\CourseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Course\Filament\Resources\CourseResource;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
