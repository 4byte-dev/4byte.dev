<?php

namespace Modules\Article\Providers;

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Article';

    protected string $nameLower = 'article';

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
            ->prefix('makale')
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
            ->prefix('api/article')
            ->name('api.article.')
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
