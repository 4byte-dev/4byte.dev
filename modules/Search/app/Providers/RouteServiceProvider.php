<?php

namespace Modules\Search\Providers;

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Search';

    protected string $nameLower = 'search';

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapWebRoutes();
        $this->mapApiRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->prefix('ara')
            ->name("{$this->nameLower}.")
            ->group(module_path($this->name, '/routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware(['web', 'auth', BatchLogsActivity::class])
            ->prefix("api/{$this->nameLower}")
            ->name("api.{$this->nameLower}.")
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
