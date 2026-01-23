<?php

namespace Modules\Entry\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Entry\Models\Entry;
use Modules\Entry\Policies\EntryPolicy;
use Modules\Entry\Services\EntryService;
use Modules\React\Services\ReactService;
use Modules\Recommend\Services\FeedService;
use Nwidart\Modules\Traits\PathNamespace;

class EntryServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Entry';

    protected string $nameLower = 'entry';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerPolicies();
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
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            EntryService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Entry::class, EntryPolicy::class);
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
            class: Entry::class,
            callback: fn ($slug) => app(EntryService::class)->getId($slug)
        );
    }

    /**
     * Register to Feed.
     */
    protected function registerFeed(): void
    {
        FeedService::registerHandler(
            name: $this->nameLower,
            isFilter: false,
            callback: fn ($slug) => app(EntryService::class)->getData($slug)
        );
    }
}
