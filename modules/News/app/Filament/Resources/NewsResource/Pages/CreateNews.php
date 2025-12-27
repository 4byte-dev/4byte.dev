<?php

namespace Modules\News\Filament\Resources\NewsResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\News\Filament\Resources\NewsResource;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;
}
