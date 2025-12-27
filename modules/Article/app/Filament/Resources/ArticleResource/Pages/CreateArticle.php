<?php

namespace Modules\Article\Filament\Resources\ArticleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Article\Filament\Resources\ArticleResource;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
