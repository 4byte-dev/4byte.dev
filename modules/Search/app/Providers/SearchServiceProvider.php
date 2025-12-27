<?php

namespace Modules\Search\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Search\Services\SearchService;
use Nwidart\Modules\Traits\PathNamespace;

class SearchServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Search';

    protected string $nameLower = 'search';

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
            SearchService::class,
        ];
    }
}
