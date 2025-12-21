<?php

namespace Modules\Entry\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class EntryPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Entry';
    }

    public function getId(): string
    {
        return 'entry';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
