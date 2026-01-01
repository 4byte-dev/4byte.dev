<?php

namespace Modules\Entry\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Modules\Entry\Models\Entry;
use Modules\Entry\Observers\EntryObserver;
use Modules\Entry\Tests\TestCase;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class EntryObserverTest extends TestCase
{
    private GorseService|MockInterface $gorse;

    private EntryObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gorse    = Mockery::mock(GorseService::class);
        $this->observer = new EntryObserver($this->gorse);
    }

    public function test_saved_inserts_item_to_gorse(): void
    {
        $entry = Entry::factory()->make([
            'id'      => 1,
            'user_id' => 5,
        ]);

        $this->gorse->shouldReceive('insertItem')
            ->once()
            ->with(Mockery::type(GorseItem::class));

        $this->observer->saved($entry);
    }

    public function test_updated_clears_cache(): void
    {
        $entry = Entry::factory()->make([
            'id' => 1,
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('entry:1');

        $this->observer->updated($entry);
    }

    public function test_deleted_removes_from_gorse_and_clears_cache(): void
    {
        $entry = Entry::factory()->make([
            'id'   => 1,
            'slug' => 'test-slug',
        ]);

        $this->gorse->shouldReceive('deleteItem')
            ->once()
            ->with('entry:1');

        Cache::shouldReceive('forget')->with('entry:test-slug:id');
        Cache::shouldReceive('forget')->with('entry:1');
        Cache::shouldReceive('forget')->with('entry:1:likes');
        Cache::shouldReceive('forget')->with('entry:1:dislikes');

        $this->observer->deleted($entry);
    }
}
