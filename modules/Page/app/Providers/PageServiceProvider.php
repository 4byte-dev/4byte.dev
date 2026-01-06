<?php

namespace Modules\Page\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Page\Console\Commands\SchedulePageCommand;
use Modules\Page\Models\Page;
use Modules\Page\Observers\PageObserver;
use Modules\Page\Policies\PagePolicy;
use Modules\Page\Services\PageService;
use Modules\Search\Services\SearchService;
use Nwidart\Modules\Traits\PathNamespace;

class PageServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Page';

    protected string $nameLower = 'page';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerObservers();
        $this->registerPolicies();
        $this->registerSearch();
        $this->registerCommands();
        $this->registerTranslations();
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
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            PageService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Page::class, PagePolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Page::observe(PageObserver::class);
    }

    /**
     * Register commands in the format of Command::class.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            SchedulePageCommand::class,
        ]);
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
     * Register to React for make it searchable.
     */
    protected function registerSearch(): void
    {
        SearchService::registerHandler(
            index: 'pages',
            callback: fn ($hit) => app(PageService::class)->getData($hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id'],
            sortableAttributes: ['updated_at']
        );
    }
}
