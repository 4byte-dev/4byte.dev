<?php

namespace Modules\React\Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Article\Models\Article;
use Modules\React\Models\Count;
use Modules\React\Models\Like;
use Tests\TestCase;

class UpdateReactCountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_updates_article_counts_correctly()
    {
        $article = Article::factory()->create();

        Like::factory()->count(5)->create([
            'likeable_type' => Article::class,
            'likeable_id'   => $article->id,
        ]);

        Count::updateOrCreate([
            'countable_type' => Article::class,
            'countable_id'   => $article->id,
            'filter'         => 'likes',
        ], ['count' => 999]);

        $cacheKey = "react:counts:article:{$article->id}:likes";
        Cache::forever($cacheKey, 999);

        $this->artisan('react:update-counts')
            ->assertExitCode(0);

        $this->assertDatabaseHas('counts', [
            'countable_type' => Article::class,
            'countable_id'   => $article->id,
            'filter'         => 'likes',
            'count'          => 5,
        ]);

        $cacheKey = "react:counts:article:{$article->id}:likes";
        $this->assertEquals(5, Cache::get($cacheKey));
    }
}
