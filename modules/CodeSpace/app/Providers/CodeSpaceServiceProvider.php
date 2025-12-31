<?php

namespace Modules\CodeSpace\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Observers\CodeSpaceObserver;
use Modules\CodeSpace\Policies\CodeSpacePolicy;
use Modules\CodeSpace\Services\CodeSpaceService;
use Nwidart\Modules\Traits\PathNamespace;

class CodeSpaceServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'CodeSpace';

    protected string $nameLower = 'codespace';

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
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
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
            CodeSpaceService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(CodeSpace::class, CodeSpacePolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        CodeSpace::observe(CodeSpaceObserver::class);
    }

    /**
     * Register console publishes.
     */
    protected function registerPublishableResources(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                module_path($this->name, '$SEEDERS_PATH$')      => database_path("seeders/{$this->name}"),
                module_path($this->name, 'database/migrations') => database_path('migrations/'),
            ], $this->nameLower);
        }
    }
}
