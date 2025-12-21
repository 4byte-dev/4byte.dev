<?php

namespace Modules\News\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class NewsPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'News';
    }

    public function getId(): string
    {
        return 'news';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
