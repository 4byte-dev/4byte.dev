<?php

namespace Modules\Page\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class PagePlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Page';
    }

    public function getId(): string
    {
        return 'page';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
