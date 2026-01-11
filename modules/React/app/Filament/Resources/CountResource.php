<?php

namespace Modules\React\Filament\Resources;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Resources\Resource;
use Modules\React\Models\Count;

class CountResource extends Resource
{
    use HasPanelShield;

    protected static ?string $model = Count::class;

    protected static bool $shouldRegisterNavigation = false;
}
