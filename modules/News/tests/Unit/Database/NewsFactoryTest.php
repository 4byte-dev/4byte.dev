<?php

namespace Modules\News\Tests\Unit\Database;

use App\Models\User;
use Illuminate\Support\Str;
use Modules\News\Models\News;
use Modules\News\Tests\TestCase;

class NewsFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_news(): void
    {
        $news = News::factory()->create();

        $this->assertInstanceOf(News::class, $news);
        $this->assertContains($news->status, ['DRAFT', 'PUBLISHED', 'PENDING']);
    }

    public function test_it_creates_news_linked_to_user(): void
    {
        $user = User::factory()->create();
        $news = News::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $news->user_id);
    }

    public function test_slug_is_a_valid_slug(): void
    {
        $news = News::factory()->create();

        $slugBase = preg_replace('/-\d+$/', '', $news->slug);

        $this->assertSame(
            Str::slug($news->title),
            $slugBase
        );
    }

    public function test_slug_contains_only_slug_characters(): void
    {
        $news = News::factory()->create();

        $this->assertMatchesRegularExpression(
            '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            preg_replace('/-\d+$/', '', $news->slug)
        );
    }

    public function test_factory_creates_unique_news(): void
    {
        $newsItems = News::factory()->count(10)->create();

        $this->assertCount(
            10,
            $newsItems->pluck('slug')->unique()
        );
    }
}
