<?php

namespace Modules\Article\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Modules\Admin\Filament\Components\SpatieMediaLibraryFileUpload;
use Modules\Admin\Filament\Components\SpatieMediaLibraryMarkdownEditor;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Filament\Resources\ArticleResource\Pages;
use Modules\Article\Models\Article;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class ArticleResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationLabel(): string
    {
        return __('Articles');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        Forms\Components\Section::make(__('Main Content'))
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label(__('Title'))
                                            ->required()
                                            ->reactive(),
                                        Forms\Components\TextInput::make('slug')
                                            ->label(__('Slug'))
                                            ->required()
                                            ->unique(Article::class, 'slug', fn ($record) => $record)
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('generateSlug')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->tooltip(__('Generate from slug'))
                                                    ->action(function ($state, $set, $get) {
                                                        $set('slug', \Str::slug($get('title')));
                                                    })
                                            ),
                                    ]),

                                Forms\Components\Textarea::make('excerpt')
                                    ->label(__('Excerpt')),

                                Forms\Components\Repeater::make('sources')
                                    ->label(__('Sources'))
                                    ->schema([
                                        Forms\Components\TextInput::make('url')
                                            ->label(__('Url'))
                                            ->required()
                                            ->url(),
                                        Forms\Components\DatePicker::make('date')
                                            ->label(__('Date'))
                                            ->required()
                                            ->default(Carbon::now()),
                                    ]),

                                SpatieMediaLibraryMarkdownEditor::make('content')
                                    ->label(__('Content'))
                                    ->extraAttributes(['style' => 'min-height: 790px;'])
                                    ->collection('content'),
                            ])
                            ->columnSpan(9),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make(__('Article Settings'))
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('image')
                                            ->label(__('Image'))
                                            ->image()
                                            ->imageEditor()
                                            ->imagePreviewHeight('150')
                                            ->dehydrated(false)
                                            ->collection('cover'),

                                        Forms\Components\Select::make('categories')
                                            ->label(__('Categories'))
                                            ->multiple()
                                            ->relationship('categories', 'name'),

                                        Forms\Components\Select::make('tags')
                                            ->label(__('Tags'))
                                            ->multiple()
                                            ->relationship('tags', 'name'),

                                        Forms\Components\Select::make('user_id')
                                            ->searchable()
                                            ->required()
                                            ->label(__('User'))
                                            ->relationship('user', 'name'),

                                        Forms\Components\Select::make('status')
                                            ->required()
                                            ->label(__('Status'))
                                            ->options(ArticleStatus::class)
                                            ->default(ArticleStatus::DRAFT)
                                            ->live()
                                            ->afterStateUpdated(function (ArticleStatus $state, callable $set) {
                                                if ($state === ArticleStatus::PUBLISHED) {
                                                    $set('published_at', Carbon::now());
                                                } else {
                                                    $set('published_at', null);
                                                }
                                            }),

                                        Forms\Components\DateTimePicker::make('published_at')
                                            ->label(__('Published At'))
                                            ->required(fn ($get) => $get('status') === ArticleStatus::PENDING->value),
                                    ]),
                            ])->columnSpan(3),

                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ActivitylogRelationManager::class,
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('Image'))
                    ->grow(false)
                    ->getStateUsing(function ($record) {
                        return $record->getFirstMediaUrl('cover');
                    }),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('User'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->sortable()
                    ->label(__('Publish'))
                    ->dateTime(),
            ])
            ->filters([
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit'   => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
