<?php

namespace Packages\User;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Packages\React\Services\ReactService;
use Packages\Recommend\Services\FeedService;
use Packages\Search\Services\SearchService;
use Packages\User\Models\User;
use Packages\User\Models\UserProfile;
use Packages\User\Observers\UserObserver;
use Packages\User\Observers\UserProfileObserver;
use Packages\User\Policies\UserPolicy;

class UserProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadPolicies();
        $this->loadObservers();
        $this->loadRoutes();
        $this->loadFactories();
        $this->loadSeeders();
        $this->loadMigrations();
        $this->configureReact();
        $this->configureFeed();
    }

    public function loadPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
    }

    public function loadObservers(): void
    {
        User::observe(UserObserver::class);
        UserProfile::observe(UserProfileObserver::class);
    }

    public function loadRoutes(): void
    {
        Route::middleware('web')
            ->namespace('Packages\User\Http\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }

    protected function loadFactories(): void
    {
        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');
    }

    protected function loadSeeders(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/seeders' => database_path('seeders/packages/user'),
            ], 'seeders');
        }
    }

    protected function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations/'),
            ], 'migrations');
        }
    }

    protected function configureSearch(): void
    {
        SearchService::registerHandler(
            index: 'users',
            callback: fn ($hit) => app(Services\UserService::class)->getData($hit['id']),
            searchableAttributes: ['name', 'username'],
            filterableAttributes: ['id'],
            sortableAttributes: ['created_at']
        );
    }

    protected function configureReact(): void
    {
        ReactService::registerHandler(
            name: 'user',
            class: User::class,
            callback: fn ($slug) => app(Services\UserService::class)->getId($slug)
        );
    }

    protected function configureFeed(): void
    {
        FeedService::registerHandler(
            name: 'user',
            isFilter: true,
            callback: fn ($slug) => app(Services\UserService::class)->getId($slug)
        );
    }
}
