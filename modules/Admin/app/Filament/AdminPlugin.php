<?php

namespace Modules\Admin\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class AdminPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Admin';
    }

    public function getId(): string
    {
        return 'admin';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
