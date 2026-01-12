<?php

namespace Modules\Article\Tests\Unit\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Article\Actions\UpdateArticleAction;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;

class UpdateArticleActionTest extends TestCase
{
    private UpdateArticleAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new UpdateArticleAction();
    }

    public function test_it_updates_article_fields(): void
    {
        $article = Article::factory()->create([
            'title'   => 'Old Title',
            'excerpt' => 'Old Excerpt',
        ]);

        $data = [
            'title'     => 'New Title',
            'excerpt'   => 'New Excerpt',
            'published' => true,
        ];

        $updatedArticle = $this->action->execute($article, $data);

        $this->assertEquals('New Title', $updatedArticle->title);
        $this->assertEquals('New Excerpt', $updatedArticle->excerpt);
        $this->assertEquals(ArticleStatus::PUBLISHED, $updatedArticle->status);

        $this->assertFalse(Cache::has("article:{$article->id}"));
    }

    public function test_it_updates_slug_when_title_changes(): void
    {
        $article = Article::factory()->create(['title' => 'Old Title', 'slug' => 'old-title']);

        $this->assertDatabaseMissing(Article::class, ['slug' => 'new-title']);

        $data           = ['title' => 'New Title'];
        $updatedArticle = $this->action->execute($article, $data);

        $this->assertEquals('new-title', $updatedArticle->slug);
    }

    public function test_it_handles_slug_collisions_on_update(): void
    {
        Article::factory()->create(['title' => 'Existing Title', 'slug' => 'existing-title']);
        $article = Article::factory()->create(['title' => 'My Article', 'slug' => 'my-article']);

        $data           = ['title' => 'Existing Title'];
        $updatedArticle = $this->action->execute($article, $data);

        $this->assertEquals('existing-title-1', $updatedArticle->slug);
    }

    public function test_it_syncs_relations(): void
    {
        $article  = Article::factory()->create();
        $category = Category::factory()->create();
        $tag      = Tag::factory()->create();

        $data = [
            'title'      => $article->title,
            'categories' => [$category->slug],
            'tags'       => [$tag->slug],
        ];

        $this->action->execute($article, $data);

        $this->assertTrue($article->categories->contains($category));
        $this->assertTrue($article->tags->contains($tag));
    }

    public function test_it_updates_image(): void
    {
        Storage::fake('public');
        $article = Article::factory()->create();
        $file    = UploadedFile::fake()->image('new-cover.jpg');

        $data = ['title' => $article->title];

        $this->action->execute($article, $data, $file);

        $this->assertTrue($article->hasMedia('cover'));
    }
}
