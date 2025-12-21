<?php

namespace Modules\Article\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class ArticlePlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Article';
    }

    public function getId(): string
    {
        return 'article';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
