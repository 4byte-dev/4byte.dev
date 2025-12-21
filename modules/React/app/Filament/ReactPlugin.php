<?php

namespace Modules\React\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ReactPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'React';
    }

    public function getId(): string
    {
        return 'react';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
