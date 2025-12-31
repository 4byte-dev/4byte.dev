<?php

namespace Modules\CodeSpace\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CodeSpacePlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'CodeSpace';
    }

    public function getId(): string
    {
        return 'codespace';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
