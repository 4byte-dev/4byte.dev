<?php

namespace Modules\Admin\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Rmsramos\Activitylog\Resources\ActivitylogResource as BaseActivityLogPage;

class ActivityLog extends BaseActivityLogPage
{
    use HasPanelShield;
}
