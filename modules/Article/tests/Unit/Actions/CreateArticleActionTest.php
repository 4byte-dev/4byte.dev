<?php

namespace Modules\Article\Tests\Unit\Actions;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Article\Actions\CreateArticleAction;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;

class CreateArticleActionTest extends TestCase
{
    private CreateArticleAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new CreateArticleAction();
    }

    public function test_it_creates_draft_article(): void
    {
        $user = User::factory()->create();
        $data = [
            'title'     => 'Test Article',
            'published' => false,
        ];

        $article = $this->action->execute($data, null, $user->id);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals('Test Article', $article->title);
        $this->assertEquals('DRAFT', $article->status);
        $this->assertNull($article->published_at);
        $this->assertNotEmpty($article->slug);
        $this->assertEquals($user->id, $article->user_id);
    }

    public function test_it_creates_published_article_with_full_data(): void
    {
        $user     = User::factory()->create();
        $category = Category::factory()->create();
        $tag      = Tag::factory()->create();

        $data = [
            'title'      => 'Full Article',
            'excerpt'    => 'An excerpt',
            'content'    => 'Some content',
            'published'  => true,
            'categories' => [$category->slug],
            'tags'       => [$tag->slug],
            'sources'    => [['url' => 'https://example.com', 'date' => now()->toDateString()]],
        ];

        $article = $this->action->execute($data, null, $user->id);

        $this->assertEquals('PUBLISHED', $article->status);
        $this->assertNotNull($article->published_at);
        $this->assertEquals('An excerpt', $article->excerpt);
        $this->assertTrue($article->categories->contains($category));
        $this->assertTrue($article->tags->contains($tag));
        $this->assertEquals([['url' => 'https://example.com', 'date' => now()->toDateString()]], $article->sources);
    }

    public function test_it_handles_image_upload(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');
        $file = UploadedFile::fake()->image('cover.jpg');

        $data = ['title' => 'Image Article'];

        $article = $this->action->execute($data, $file, $user->id);

        $this->assertTrue($article->hasMedia('cover'));
    }

    public function test_it_generates_unique_slugs(): void
    {
        $user = User::factory()->create();
        Article::factory()->create(['title' => 'Unique Title', 'slug' => 'unique-title']);

        $data    = ['title' => 'Unique Title'];
        $article = $this->action->execute($data, null, $user->id);

        $this->assertEquals('unique-title-1', $article->slug);
    }
}
