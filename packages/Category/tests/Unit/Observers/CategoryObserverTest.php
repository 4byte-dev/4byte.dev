<?php

namespace Packages\Category\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Category\Models\Category;
use Packages\Category\Observers\CategoryObserver;
use Packages\Category\Tests\TestCase;

class CategoryObserverTest extends TestCase
{
    public function test_it_clears_old_slug_cache_when_slug_changes(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('category:old-slug:id');

        Cache::shouldReceive('forget')
            ->once()
            ->with('category:1');

        $category       = new Category();
        $category->id   = 1;
        $category->slug = 'old-slug';
        $category->syncOriginal();

        $category->slug = 'new-slug';

        (new CategoryObserver())->updated($category);
    }

    public function test_it_does_not_clear_slug_cache_if_slug_not_changed(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('category:1');

        Cache::shouldReceive('forget')
            ->never()
            ->with('category:slug:id');

        $category       = new Category();
        $category->id   = 1;
        $category->slug = 'slug';
        $category->syncOriginal();

        (new CategoryObserver())->updated($category);
    }

    public function test_update_always_clears_id_cache(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('category:1');

        $category       = new Category();
        $category->id   = 1;
        $category->slug = 'slug';
        $category->syncOriginal();

        (new CategoryObserver())->updated($category);
    }

    public function test_it_clears_all_related_cache_on_delete(): void
    {
        Cache::shouldReceive('forget')->once()->with('category:slug:id');
        Cache::shouldReceive('forget')->once()->with('category:1');
        Cache::shouldReceive('forget')->once()->with('category:1:articles');
        Cache::shouldReceive('forget')->once()->with('category:1:news');
        Cache::shouldReceive('forget')->once()->with('category:1:followers');

        $category       = new Category();
        $category->id   = 1;
        $category->slug = 'slug';

        (new CategoryObserver())->deleted($category);
    }
}
