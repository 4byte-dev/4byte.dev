<?php

namespace Modules\Article\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;

class RecentArticles extends BaseWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 24;

    protected int|string|array $columnSpan = 6;

    /** @var int|array<int>|null */
    protected int | array | null $columns = 6;

    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('filament.admin.resources.articles.edit', ['record' => $record]);
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('view')
                ->icon('heroicon-o-eye')
                ->url(fn ($record) => route('article.view', ['slug' => $record->slug]))
                ->openUrlInNewTab(),

            Action::make('edit')
                ->icon('heroicon-o-pencil')
                ->url(fn ($record) => route('filament.admin.resources.articles.edit', $record))
                ->color('primary'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('categories')
                ->label(__('Categories'))
                ->multiple()
                ->relationship('categories', 'name'),
            Tables\Filters\SelectFilter::make('tags')
                ->label(__('Tags'))
                ->multiple()
                ->relationship('tags', 'name'),
            Tables\Filters\SelectFilter::make('user')
                ->label(__('User'))
                ->relationship('user', 'name')
                ->multiple(),
            Tables\Filters\SelectFilter::make('status')
                ->label(__('Status'))
                ->options(ArticleStatus::class),
        ];
    }

    /**
     * @return Builder<Article>
     */
    protected function getTableQuery(): Builder
    {
        return Article::query()
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->label(__('Title'))->searchable(),
            Tables\Columns\TextColumn::make('status')
                ->label(__('Status'))
                ->badge(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(__('Date'))
                ->dateTime('d M Y, H:i'),
        ];
    }
}
