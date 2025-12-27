<?php

namespace Modules\User\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\React\Services\ReactService;
use Modules\Recommend\Services\FeedService;
use Modules\Search\Services\SearchService;
use Modules\User\Models\User;
use Modules\User\Models\UserProfile;
use Modules\User\Observers\UserObserver;
use Modules\User\Observers\UserProfileObserver;
use Modules\User\Policies\UserPolicy;
use Modules\User\Services\UserService;
use Nwidart\Modules\Traits\PathNamespace;

class UserServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'User';

    protected string $nameLower = 'user';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerPublishableResources();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->loadFactoriesFrom(module_path($this->name, 'database/factories'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            UserService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(User::class, UserPolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        User::observe(UserObserver::class);
        UserProfile::observe(UserProfileObserver::class);
    }

    /**
     * Register console publishes.
     */
    protected function registerPublishableResources(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                module_path($this->name, 'database/seeders')    => database_path("seeders/{$this->name}"),
                module_path($this->name, 'database/migrations') => database_path('migrations/'),
            ], $this->nameLower);
        }
    }

    protected function registerSearch(): void
    {
        SearchService::registerHandler(
            index: 'users',
            callback: fn ($hit) => app(UserService::class)->getData($hit['id']),
            searchableAttributes: ['name', 'username'],
            filterableAttributes: ['id'],
            sortableAttributes: ['created_at']
        );
    }

    protected function registerReact(): void
    {
        ReactService::registerHandler(
            name: $this->nameLower,
            class: User::class,
            callback: fn ($slug) => app(UserService::class)->getId($slug)
        );
    }

    protected function registerFeed(): void
    {
        FeedService::registerHandler(
            name: $this->nameLower,
            isFilter: true,
            callback: fn ($slug) => app(UserService::class)->getId($slug)
        );
    }
}
