<?php

namespace Modules\Article\Tests\Unit\Actions;

use Modules\Article\Actions\PublishArticleAction;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class PublishArticleActionTest extends TestCase
{
    public function test_it_publishes_an_article(): void
    {
        $article = Article::factory()->create([
            'status'       => 'DRAFT',
            'published_at' => null,
        ]);

        $action           = new PublishArticleAction();
        $publishedArticle = $action->execute($article);

        $this->assertEquals('PUBLISHED', $publishedArticle->status);
        $this->assertNotNull($publishedArticle->published_at);
        $this->assertDatabaseHas(Article::class, [
            'id'     => $article->id,
            'status' => 'PUBLISHED',
        ]);
    }
}
