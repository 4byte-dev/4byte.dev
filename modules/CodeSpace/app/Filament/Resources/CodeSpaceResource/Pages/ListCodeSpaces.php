<?php

namespace Modules\CodeSpace\Filament\Resources\CodeSpaceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\CodeSpace\Filament\Resources\CodeSpaceResource;

class ListCodeSpaces extends ListRecords
{
    protected static string $resource = CodeSpaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
