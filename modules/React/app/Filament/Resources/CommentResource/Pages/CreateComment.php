<?php

namespace Modules\React\Filament\Resources\CommentResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Modules\React\Filament\Resources\CommentResource;

class CreateComment extends CreateRecord
{
    protected static string $resource = CommentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::uuid();

        return $data;
    }
}
