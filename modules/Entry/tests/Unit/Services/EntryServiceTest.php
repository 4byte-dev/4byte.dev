<?php

namespace Modules\Entry\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Entry\Models\Entry;
use Modules\Entry\Services\EntryService;
use Modules\Entry\Tests\TestCase;

class EntryServiceTest extends TestCase
{
    protected EntryService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->service = app(EntryService::class);
    }

    public function test_get_data_returns_entry_data(): void
    {
        $entry = Entry::factory()->create();

        $data = $this->service->getData($entry->id);

        $this->assertEquals($entry->slug, $data->slug);
        $this->assertEquals($entry->content, $data->content);

        $this->assertTrue(Cache::has("entry:{$entry->id}"));
    }

    public function test_it_can_get_entry_id_by_slug(): void
    {
        $entry = Entry::factory()->create();

        $id = $this->service->getId($entry->slug);

        $this->assertEquals($entry->id, $id);
        $this->assertTrue(Cache::has("entry:{$entry->slug}:id"));
    }
}
