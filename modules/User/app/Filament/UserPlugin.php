<?php

namespace Modules\User\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class UserPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'User';
    }

    public function getId(): string
    {
        return 'user';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
