<?php

namespace Modules\Tag\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\React\Services\ReactService;
use Modules\Recommend\Services\FeedService;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;
use Modules\Tag\Observers\TagObserver;
use Modules\Tag\Observers\TagProfileObserver;
use Modules\Tag\Policies\TagPolicy;
use Modules\Tag\Services\TagService;
use Nwidart\Modules\Traits\PathNamespace;

class TagServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Tag';

    protected string $nameLower = 'tag';

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
            TagService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Tag::class, TagPolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Tag::observe(TagObserver::class);
        TagProfile::observe(TagProfileObserver::class);
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
            class: Tag::class,
            callback: fn ($slug) => app(TagService::class)->getId($slug)
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
            callback: fn ($slug) => app(TagService::class)->getId($slug)
        );
    }
}
