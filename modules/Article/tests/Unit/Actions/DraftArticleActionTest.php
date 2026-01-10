<?php

namespace Modules\Article\Tests\Unit\Actions;

use Modules\Article\Actions\DraftArticleAction;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class DraftArticleActionTest extends TestCase
{
    public function test_it_drafts_an_article(): void
    {
        $article = Article::factory()->create([
            'status'       => 'PUBLISHED',
            'published_at' => now(),
        ]);

        $action         = new DraftArticleAction();
        $draftedArticle = $action->execute($article);

        $this->assertEquals('DRAFT', $draftedArticle->status);
        $this->assertNull($draftedArticle->published_at);
        $this->assertDatabaseHas(Article::class, [
            'id'           => $article->id,
            'status'       => 'DRAFT',
            'published_at' => null,
        ]);
    }
}
