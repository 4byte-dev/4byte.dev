<?php

namespace Packages\Article\Tests\Unit\Models;

use App\Models\User;
use Packages\Article\Models\Article;
use Packages\Article\Tests\TestCase;
use Packages\Category\Models\Category;
use Packages\Tag\Models\Tag;
use Spatie\Activitylog\Models\Activity;

class ArticleTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $article = new Article();

        $this->assertEquals(
            [
                'title',
                'slug',
                'excerpt',
                'content',
                'status',
                'sources',
                'published_at',
                'user_id',
            ],
            $article->getFillable()
        );
    }

    public function test_casts_are_correct(): void
    {
        $article = new Article();

        $this->assertEquals('datetime', $article->getCasts()['published_at']);
        $this->assertEquals('array', $article->getCasts()['sources']);
    }

    public function test_it_belongs_to_user(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $article->user);
        $this->assertEquals($user->id, $article->user->id);
    }

    public function test_it_belongs_to_many_categories(): void
    {
        $article  = Article::factory()->create();
        $category = Category::factory()->create();

        $article->categories()->attach($category);

        $this->assertTrue($article->categories->contains($category));
    }

    public function test_it_belongs_to_many_tags(): void
    {
        $article = Article::factory()->create();
        $tag     = Tag::factory()->create();

        $article->tags()->attach($tag);

        $this->assertTrue($article->tags->contains($tag));
    }

    public function test_it_logs_activity_on_create(): void
    {
        $article = Article::factory()->create([
            'title' => 'New Article',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('article', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame('New Article', $activity->properties['attributes']['title']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $article = Article::factory()->create(['title' => 'Old Title']);

        $article->update(['title' => 'New Title']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame('New Title', $activity->properties['attributes']['title']);
        $this->assertSame('Old Title', $activity->properties['old']['title']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $article = Article::factory()->create();

        $initialCount = Activity::count();

        $article->update(['title' => $article->title]);

        $this->assertSame($initialCount, Activity::count());
    }

    public function test_it_is_searchable_only_when_published(): void
    {
        $draftArticle     = Article::factory()->create(['status' => 'DRAFT']);
        $publishedArticle = Article::factory()->create(['status' => 'PUBLISHED']);

        $this->assertFalse($draftArticle->shouldBeSearchable());
        $this->assertTrue($publishedArticle->shouldBeSearchable());
    }

    public function test_searchable_array_structure(): void
    {
        $article = Article::factory()->create();

        $array = $article->toSearchableArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertEquals($article->title, $array['title']);
    }
}
