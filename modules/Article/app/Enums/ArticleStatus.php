<?php

namespace Modules\Article\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ArticleStatus: string implements HasLabel, HasColor
{
    case DRAFT     = 'DRAFT';
    case PENDING   = 'PENDING';
    case PUBLISHED = 'PUBLISHED';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRAFT     => 'Draft',
            self::PENDING   => 'Pending',
            self::PUBLISHED => 'Published',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT     => 'gray',
            self::PENDING   => 'warning',
            self::PUBLISHED => 'success',
        };
    }
}
