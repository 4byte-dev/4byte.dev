<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/app.php';

$classesToPreload = [
    \Illuminate\Http\Request::class,
    \Illuminate\Http\Response::class,
    \Illuminate\Routing\Router::class,
    \Illuminate\Foundation\Application::class,

    \Illuminate\Database\Eloquent\Model::class,
    \Illuminate\Database\Eloquent\Builder::class,
    \Illuminate\Database\Query\Builder::class,
    \Illuminate\Database\Eloquent\Relations\Relation::class,
    \Illuminate\Database\Eloquent\Collection::class,

    \Illuminate\Container\Container::class,
    \Illuminate\Support\Collection::class,
    \Illuminate\Support\Str::class,
    \Illuminate\Support\Arr::class,
    \Illuminate\Support\Facades\Facade::class,

    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class,

    \Illuminate\Auth\AuthManager::class,
    \Illuminate\Auth\SessionGuard::class,
    \Illuminate\Auth\EloquentUserProvider::class,

    \Illuminate\Queue\Worker::class,
    \Illuminate\Queue\QueueManager::class,
    \Illuminate\Queue\Jobs\Job::class,

    \Modules\Admin\Providers\AdminServiceProvider::class,
    \Modules\Article\Providers\ArticleServiceProvider::class,
    \Modules\Category\Providers\CategoryServiceProvider::class,
    \Modules\CodeSpace\Providers\CodeSpaceServiceProvider::class,
    \Modules\Course\Providers\CourseServiceProvider::class,
    \Modules\Entry\Providers\EntryServiceProvider::class,
    \Modules\News\Providers\NewsServiceProvider::class,
    \Modules\Page\Providers\PageServiceProvider::class,
    \Modules\React\Providers\ReactServiceProvider::class,
    \Modules\Recommend\Providers\RecommendServiceProvider::class,
    \Modules\Search\Providers\SearchServiceProvider::class,
    \Modules\Tag\Providers\TagServiceProvider::class,
    \Modules\User\Providers\UserServiceProvider::class,
];

function preloadClass($class) {
    if (class_exists($class, false)) {
        return;
    }

    if (!class_exists($class)) {
        return;
    }

    $rc = new ReflectionClass($class);
    foreach ($rc->getMethods() as $method) {
        if ($method->isPublic() && !$method->isAbstract()) {
            $method->getStaticVariables();
        }
    }

    $parent = $rc->getParentClass();
    if ($parent) {
        preloadClass($parent->getName());
    }

    foreach ($rc->getInterfaces() as $interface) {
        preloadClass($interface->getName());
    }
}

function preloadDirectory($dir, $pattern) {
    if (!is_dir($dir)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && preg_match($pattern, $file->getPathname())) {
            require_once $file->getPathname();
        }
    }
}

foreach ($classesToPreload as $class) {
    preloadClass($class);
}

preloadDirectory(__DIR__ . '/app/Http/Controllers', '/\.php$/');
preloadDirectory(__DIR__ . '/app/Models', '/\.php$/');
preloadDirectory(__DIR__ . '/app/Jobs', '/\.php$/');
preloadDirectory(__DIR__ . '/app/Providers', '/\.php$/');

gc_collect_cycles();
