<?php

namespace Modules\Article\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;

class ArticleStatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 12;

    protected int | string | array $columnSpan = 4;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        return [
            Stat::make(__('Articles'), Article::where('status', ArticleStatus::PUBLISHED)->count())
                ->descriptionIcon('heroicon-o-document-text')
                ->color('success'),
        ];
    }
}
