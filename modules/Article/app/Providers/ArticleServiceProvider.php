<?php

namespace Modules\Article\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Article\Console\Commands\ScheduleArticleCommand;
use Modules\Article\Models\Article;
use Modules\Article\Observers\ArticleObserver;
use Modules\Article\Policies\ArticlePolicy;
use Modules\Article\Services\ArticleService;
use Modules\React\Services\ReactService;
use Modules\Recommend\Services\FeedService;
use Modules\Search\Services\SearchService;
use Nwidart\Modules\Traits\PathNamespace;

class ArticleServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Article';

    protected string $nameLower = 'article';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerObservers();
        $this->registerCommands();
        $this->registerTranslations();
        $this->registerPublishableResources();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->loadFactoriesFrom(module_path($this->name, 'database/factories'));
        $this->registerSearch();
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
            ArticleService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Article::observe(ArticleObserver::class);
    }

    /**
     * Register commands in the format of Command::class.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ScheduleArticleCommand::class,
        ]);
    }

    /**
     * Register translations.
     */
    protected function registerTranslations(): void
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
            index: 'articles',
            callback: fn ($hit) => app(ArticleService::class)->getData($hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id'],
            sortableAttributes: ['updated_at']
        );
    }

    /**
     * Register to React.
     */
    protected function registerReact(): void
    {
        ReactService::registerHandler(
            name: $this->nameLower,
            class: Article::class,
            callback: fn ($slug) => app(ArticleService::class)->getId($slug)
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
            callback: fn ($slug) => app(ArticleService::class)->getData($slug)
        );
    }
}
