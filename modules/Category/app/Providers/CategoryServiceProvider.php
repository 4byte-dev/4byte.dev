<?php

namespace Modules\Category\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Category\Models\Category;
use Modules\Category\Observers\CategoryObserver;
use Modules\Category\Policies\CategoryPolicy;
use Modules\Category\Services\CategoryService;
use Modules\React\Services\ReactService;
use Modules\Recommend\Services\FeedService;
use Nwidart\Modules\Traits\PathNamespace;

class CategoryServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Category';

    protected string $nameLower = 'category';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerObservers();
        $this->registerPublishableResources();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->loadFactoriesFrom(module_path($this->name, 'database/factories'));
        $this->registerReact();
        $this->registerFeed();
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
            CategoryService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Category::observe(CategoryObserver::class);
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

    /**
     * Register to React.
     */
    protected function registerReact(): void
    {
        ReactService::registerHandler(
            name: $this->nameLower,
            class: Category::class,
            callback: fn ($slug) => app(CategoryService::class)->getId($slug)
        );
    }

    /**
     * Register to Feed.
     */
    protected function registerFeed(): void
    {
        FeedService::registerHandler(
            name: $this->nameLower,
            isFilter: true,
            callback: fn ($slug) => app(CategoryService::class)->getId($slug)
        );
    }
}
