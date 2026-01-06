<?php

namespace Modules\Page\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Testing\Assert;
use Modules\Page\Models\Page;
use Modules\Page\Observers\PageObserver;
use Modules\Page\Tests\TestCase;

class PageObserverTest extends TestCase
{
    private PageObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new PageObserver();
    }

    public function test_updated_clears_cache_and_old_slug_cache_if_slug_changed(): void
    {
        $page = Page::factory()->create([
            'id'   => 1,
            'slug' => 'old-slug',
        ]);

        Cache::rememberForever('page:old-slug:id', fn () => $page->id);
        Cache::rememberForever('page:1', fn () => $page->id);

        $page->slug = "new-slug";
        $this->observer->updated($page);

        Assert::assertFalse(Cache::has('page:old-slug:id'));
        Assert::assertFalse(Cache::has('page:1'));
    }

    public function test_updated_clears_only_id_cache_if_slug_not_changed(): void
    {
        $page = Page::factory()->make([
            'id'   => 1,
            'slug' => 'same-slug',
        ]);

        Cache::rememberForever('page:old-slug:id', fn () => $page->id);
        Cache::rememberForever('page:1', fn () => $page->id);

        $page->excerpt = "new excerpt";
        $this->observer->updated($page);

        Assert::assertTrue(Cache::has('page:old-slug:id'));
        Assert::assertFalse(Cache::has('page:1'));
    }

    public function test_deleted_clears_all_related_cache(): void
    {
        $page = Page::factory()->create([
            'id'   => 1,
            'slug' => 'slug',
        ]);

        Cache::rememberForever('page:slug:id', fn () => $page->id);
        Cache::rememberForever('page:1', fn () => $page->id);

        $this->observer->deleted($page);

        Assert::assertFalse(Cache::has('page:slug:id'));
        Assert::assertFalse(Cache::has('page:1'));
    }
}
