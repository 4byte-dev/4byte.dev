<?php

namespace Modules\News\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\News\Models\News;
use Modules\News\Services\NewsService;
use Modules\News\Tests\TestCase;

class NewsServiceTest extends TestCase
{
    protected NewsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(NewsService::class);
    }

    public function test_get_data_returns_news_data(): void
    {
        $news = News::factory()->create(['status' => 'PUBLISHED']);

        $data = $this->service->getData($news->id);

        $this->assertEquals($news->title, $data->title);
        $this->assertEquals($news->slug, $data->slug);
        $this->assertTrue(Cache::has("news:{$news->id}"));
    }

    public function test_it_can_get_news_id_by_slug(): void
    {
        $news = News::factory()->create(['status' => 'PUBLISHED']);

        $id = $this->service->getId($news->slug);

        $this->assertEquals($news->id, $id);
        $this->assertTrue(Cache::has("news:{$news->slug}:id"));
    }
}
