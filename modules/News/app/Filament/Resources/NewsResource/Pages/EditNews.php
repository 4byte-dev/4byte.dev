<?php

namespace Modules\News\Filament\Resources\NewsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\News\Filament\Resources\NewsResource;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
