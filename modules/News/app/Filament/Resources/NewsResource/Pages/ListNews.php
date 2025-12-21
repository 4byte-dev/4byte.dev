<?php

namespace Modules\News\Filament\Resources\NewsResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\News\Filament\Resources\NewsResource;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
