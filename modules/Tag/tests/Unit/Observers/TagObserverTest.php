<?php

namespace Modules\Tag\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Tag\Models\Tag;
use Modules\Tag\Observers\TagObserver;
use Modules\Tag\Tests\TestCase;

class TagObserverTest extends TestCase
{
    private TagObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new TagObserver();
    }

    public function test_it_clears_old_slug_cache_when_slug_changes(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('tag:old-slug:id');

        Cache::shouldReceive('forget')
            ->once()
            ->with('tag:1');

        $tag       = new Tag();
        $tag->id   = 1;
        $tag->slug = 'old-slug';
        $tag->syncOriginal();

        $tag->slug = 'new-slug';

        $this->observer->updated($tag);
    }

    public function test_it_does_not_clear_slug_cache_if_slug_not_changed(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('tag:1');

        Cache::shouldReceive('forget')
            ->never()
            ->with('tag:slug:id');

        $tag       = new Tag();
        $tag->id   = 1;
        $tag->slug = 'slug';
        $tag->syncOriginal();

        $this->observer->updated($tag);
    }

    public function test_update_always_clears_id_cache(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('tag:1');

        $tag       = new Tag();
        $tag->id   = 1;
        $tag->slug = 'slug';
        $tag->syncOriginal();

        $this->observer->updated($tag);
    }

    public function test_it_clears_all_related_cache_on_delete(): void
    {
        Cache::shouldReceive('forget')->once()->with('tag:slug:id');
        Cache::shouldReceive('forget')->once()->with('tag:1');
        Cache::shouldReceive('forget')->once()->with('tag:1:articles');
        Cache::shouldReceive('forget')->once()->with('tag:1:news');
        Cache::shouldReceive('forget')->once()->with('tag:1:followers');

        $tag       = new Tag();
        $tag->id   = 1;
        $tag->slug = 'slug';

        $this->observer->deleted($tag);
    }
}
