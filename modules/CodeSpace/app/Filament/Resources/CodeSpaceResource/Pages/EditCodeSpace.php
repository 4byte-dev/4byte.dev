<?php

namespace Modules\CodeSpace\Filament\Resources\CodeSpaceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\CodeSpace\Filament\Resources\CodeSpaceResource;

class EditCodeSpace extends EditRecord
{
    protected static string $resource = CodeSpaceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
