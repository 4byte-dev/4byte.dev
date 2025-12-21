<?php

namespace Modules\Category\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CategoryPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Category';
    }

    public function getId(): string
    {
        return 'category';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
