<?php

namespace Modules\CodeSpace\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\CodeSpace\Filament\Resources\CodeSpaceResource\Pages;
use Modules\CodeSpace\Models\CodeSpace;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class CodeSpaceResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = CodeSpace::class;

    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static ?int $navigationSort = 8;

    public static function getNavigationGroup(): string
    {
        return __('CMS');
    }

    public static function getNavigationLabel(): string
    {
        return __('CodeSpaces');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(12)
                    ->schema([
                        Forms\Components\Section::make('Main Content')
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
                                            ->unique(CodeSpace::class, 'slug', fn ($record) => $record)
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('generateSlug')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->tooltip(__('Generate from slug'))
                                                    ->action(function ($state, $set, $get) {
                                                        $set('slug', \Str::uuid());
                                                    })
                                            ),
                                        Forms\Components\Select::make('user_id')
                                            ->searchable()
                                            ->required()
                                            ->label(__('User'))
                                            ->relationship('user', 'name'),
                                    ]),
                                Forms\Components\Textarea::make('files')
                                    ->label(__('Files'))
                                    ->disabled()
                                    ->rows(14)
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn ($state) => json_encode(
                                            $state,
                                            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                                        )
                                    ),
                            ]),
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label(__('User'))
                    ->relationship('user', 'name')
                    ->multiple(),
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
            'index'  => Pages\ListCodeSpaces::route('/'),
            'create' => Pages\CreateCodeSpace::route('/create'),
            'edit'   => Pages\EditCodeSpace::route('/{record}/edit'),
        ];
    }
}
