<?php

namespace Modules\React\Providers;

use App\Http\Middleware\BatchLogsActivity;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'React';

    protected string $nameLower = 'react';

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
        Route::middleware(['web', 'auth', BatchLogsActivity::class])
            ->prefix("api/{$this->nameLower}")
            ->name("api.{$this->nameLower}.")
            ->group(module_path($this->name, '/routes/api.php'));
    }
}
