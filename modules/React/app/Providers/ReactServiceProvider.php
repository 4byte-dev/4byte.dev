<?php

namespace Modules\React\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Follow;
use Modules\React\Models\Like;
use Modules\React\Models\Save;
use Modules\React\Observers\CommentObserver;
use Modules\React\Observers\DislikeObserver;
use Modules\React\Observers\FollowObserver;
use Modules\React\Observers\SaveObserver;
use Modules\React\Policies\CommentPolicy;
use Modules\React\Policies\DislikePolicy;
use Modules\React\Policies\FollowPolicy;
use Modules\React\Policies\LikePolicy;
use Modules\React\Policies\SavePolicy;
use Modules\React\Services\ReactService;
use Modules\Recommend\Services\FeedService;
use Nwidart\Modules\Traits\PathNamespace;

class ReactServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'React';

    protected string $nameLower = 'react';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
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
            ReactService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Like::class, LikePolicy::class);
        Gate::policy(Dislike::class, DislikePolicy::class);
        Gate::policy(Save::class, SavePolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Follow::class, FollowPolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Dislike::observe(DislikeObserver::class);
        Save::observe(SaveObserver::class);
        Comment::observe(CommentObserver::class);
        Follow::observe(FollowObserver::class);
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
            name: 'comment',
            class: Comment::class,
            callback: fn ($slug) => $slug
        );
    }

    /**
     * Register to Feed.
     */
    protected function registerFeed(): void
    {
        FeedService::registerHandler(
            name: 'comment',
            isFilter: false,
            callback: fn ($slug) => app(ReactService::class)->getComment($slug)
        );
    }
}
