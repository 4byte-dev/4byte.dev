<?php

namespace Modules\Entry\Filament\Resources\EntryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Modules\Entry\Filament\Resources\EntryResource;

class CreateEntry extends CreateRecord
{
    protected static string $resource = EntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::uuid();

        return $data;
    }
}
