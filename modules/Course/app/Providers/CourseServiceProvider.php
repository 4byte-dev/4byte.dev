<?php

namespace Modules\Course\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Course\Console\Commands\ScheduleCourseCommand;
use Modules\Course\Console\Commands\ScheduleLessonCommand;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Observers\CourseChapterObserver;
use Modules\Course\Observers\CourseLessonObserver;
use Modules\Course\Observers\CourseObserver;
use Modules\Course\Policies\CourseChapterPolicy;
use Modules\Course\Policies\CourseLessonPolicy;
use Modules\Course\Policies\CoursePolicy;
use Modules\Course\Services\CourseService;
use Modules\React\Services\ReactService;
use Modules\Recommend\Services\FeedService;
use Modules\Search\Services\SearchService;
use Nwidart\Modules\Traits\PathNamespace;

class CourseServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Course';

    protected string $nameLower = 'course';

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
            CourseService::class,
        ];
    }

    /**
     * Register model policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(CourseChapter::class, CourseChapterPolicy::class);
        Gate::policy(CourseLesson::class, CourseLessonPolicy::class);
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Course::observe(CourseObserver::class);
        CourseLesson::observe(CourseLessonObserver::class);
        CourseChapter::observe(CourseChapterObserver::class);
    }

    /**
     * Register commands in the format of Command::class.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ScheduleCourseCommand::class,
            ScheduleLessonCommand::class,
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
            index: 'courses',
            callback: fn ($hit) => app(CourseService::class)->getData($hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id'],
            sortableAttributes: ['updated_at']
        );
        SearchService::registerHandler(
            index: 'lessons',
            callback: fn ($hit) => app(CourseService::class)->getLessonByChapter($hit['chapter_id'], $hit['id']),
            searchableAttributes: ['title'],
            filterableAttributes: ['id', 'chapter_id'],
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
            class: Course::class,
            callback: fn ($slug) => app(CourseService::class)->getId($slug)
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
            callback: fn ($slug) => app(CourseService::class)->getData($slug)
        );
    }
}
