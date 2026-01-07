<?php

namespace Modules\Recommend\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Recommend';

    protected string $nameLower = 'recommend';

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware(['web'])
            ->prefix('api/feed')
            ->name('api.feed.')
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
