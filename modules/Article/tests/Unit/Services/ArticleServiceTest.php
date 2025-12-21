<?php

namespace Modules\Article\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Models\Article;
use Modules\Article\Services\ArticleService;
use Modules\Article\Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    protected ArticleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(ArticleService::class);
    }

    public function test_get_data_returns_article_data(): void
    {
        $article = Article::factory()->create(['status' => 'PUBLISHED']);

        $data = $this->service->getData($article->id);

        $this->assertEquals($article->title, $data->title);
        $this->assertEquals($article->slug, $data->slug);
        $this->assertTrue(Cache::has("article:{$article->id}"));
    }

    public function test_it_can_get_article_id_by_slug(): void
    {
        $article = Article::factory()->create([
            'status' => 'PUBLISHED',
        ]);

        $id = $this->service->getId($article->slug);

        $this->assertEquals($article->id, $id);
        $this->assertTrue(Cache::has("article:{$article->slug}:id"));
    }
}
