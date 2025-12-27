<?php

namespace Modules\Course\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CoursePlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Course';
    }

    public function getId(): string
    {
        return 'course';
    }

    public function boot(Panel $panel): void
    {
        // Implement boot() method.
    }
}
