<?php

namespace Modules\Recommend\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Recommend\Console\Commands\UploadRecommendations;
use Modules\Recommend\Services\FeedService;
use Modules\Recommend\Services\GorseService;
use Nwidart\Modules\Traits\PathNamespace;

class RecommendServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Recommend';

    protected string $nameLower = 'recommend';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerConfig();
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
            FeedService::class,
            GorseService::class,
        ];
    }

    /**
     * Register commands in the format of Command::class.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            UploadRecommendations::class,
        ]);
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));
        $this->publishes([
            "{$configPath}/{$this->nameLower}.php" => config_path("{$this->nameLower}.php"),
        ]);
        $this->mergeConfigFrom("{$configPath}/{$this->nameLower}.php", $this->nameLower);
    }
}
