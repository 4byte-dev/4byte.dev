<?php

namespace Packages\Article\Tests\Unit\Database;

use App\Models\User;
use Illuminate\Support\Str;
use Packages\Article\Models\Article;
use Packages\Article\Tests\TestCase;

class ArticleFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_article(): void
    {
        $article = Article::factory()->create();

        $this->assertInstanceOf(Article::class, $article);
        $this->assertNotNull($article->excerpt);
        $this->assertNotNull($article->content);
        $this->assertContains($article->status, ['DRAFT', 'PUBLISHED', 'PENDING']);
        $this->assertIsArray($article->sources);
        $this->assertTrue($this->isValidUrl($article->sources[0]['url']));
        $this->assertTrue($this->isValidDate($article->sources[0]['date']));
    }

    public function test_it_creates_article_linked_to_user(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $article->user_id);
    }

    public function test_slug_is_a_valid_slug(): void
    {
        $tag = Article::factory()->create();

        $this->assertSame(
            Str::slug($tag->title),
            $tag->slug
        );
    }

    public function test_slug_contains_only_slug_characters(): void
    {
        $article = Article::factory()->create();

        $this->assertMatchesRegularExpression(
            '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            $article->slug
        );
    }

    public function test_factory_creates_unique_articles(): void
    {
        $articles = Article::factory()->count(10)->create();

        $this->assertCount(
            10,
            $articles->pluck('slug')->unique()
        );
    }
}
