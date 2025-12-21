<?php

namespace Modules\Tag\Filament\Resources\TagResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Tag\Filament\Resources\TagResource;

class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;
}
