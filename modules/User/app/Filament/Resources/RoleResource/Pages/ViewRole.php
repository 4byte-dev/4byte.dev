<?php

namespace Modules\User\Filament\Resources\RoleResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Modules\User\Filament\Resources\RoleResource;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
