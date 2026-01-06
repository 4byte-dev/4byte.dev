<?php

namespace Modules\Page\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Page\Models\Page;
use Modules\Page\Services\PageService;
use Modules\Page\Tests\TestCase;

class PageServiceTest extends TestCase
{
    protected PageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(PageService::class);
    }

    public function test_get_data_returns_page_data(): void
    {
        $page = Page::factory()->create([
            'status' => 'PUBLISHED',
        ]);

        $data = $this->service->getData($page->id);

        $this->assertEquals($page->title, $data->title);
        $this->assertEquals($page->slug, $data->slug);
        $this->assertTrue(Cache::has("page:{$page->id}"));
    }

    public function test_it_can_get_page_id_by_slug(): void
    {
        $page = Page::factory()->create([
            'status' => 'PUBLISHED',
        ]);

        $id = $this->service->getId($page->slug);

        $this->assertEquals($page->id, $id);
        $this->assertTrue(Cache::has("page:{$page->slug}:id"));
    }
}
