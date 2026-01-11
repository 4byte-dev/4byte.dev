<?php

namespace Modules\News\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\News\Models\News;
use Modules\News\Observers\NewsObserver;
use Modules\News\Policies\NewsPolicy;
use Modules\News\Services\NewsService;
use Modules\Recommend\Services\FeedService;
use Nwidart\Modules\Traits\PathNamespace;

class NewsServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'News';

    protected string $nameLower = 'news';

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
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            NewsService::class,
        ];
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(News::class, NewsPolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        News::observe(NewsObserver::class);
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
     * Register to Feed.
     */
    protected function registerFeed(): void
    {
        FeedService::registerHandler(
            name: $this->nameLower,
            isFilter: false,
            callback: fn ($slug) => app(NewsService::class)->getData($slug)
        );
    }
}
