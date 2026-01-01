<?php

namespace Modules\News\Tests\Unit\Models;

use App\Models\User;
use Modules\Category\Models\Category;
use Modules\News\Models\News;
use Modules\News\Tests\TestCase;
use Modules\Tag\Models\Tag;
use Spatie\Activitylog\Models\Activity;

class NewsTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $news = new News();

        $this->assertEquals(
            [
                'title',
                'slug',
                'excerpt',
                'content',
                'status',
                'published_at',
                'user_id',
            ],
            $news->getFillable()
        );
    }

    public function test_casts_are_correct(): void
    {
        $news = new News();

        $this->assertEquals('datetime', $news->getCasts()['published_at']);
    }

    public function test_it_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $news = News::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $news->user);
        $this->assertEquals($user->id, $news->user->id);
    }

    public function test_it_belongs_to_many_categories(): void
    {
        $news     = News::factory()->create();
        $category = Category::factory()->create();

        $news->categories()->attach($category);

        $this->assertTrue($news->categories->contains($category));
    }

    public function test_it_belongs_to_many_tags(): void
    {
        $news = News::factory()->create();
        $tag  = Tag::factory()->create();

        $news->tags()->attach($tag);

        $this->assertTrue($news->tags->contains($tag));
    }

    public function test_it_logs_activity_on_create(): void
    {
        News::factory()->create([
            'title' => 'New News',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('news', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame('New News', $activity->properties['attributes']['title']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $news = News::factory()->create(['title' => 'Old Title']);

        $news->update(['title' => 'New Title']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame('New Title', $activity->properties['attributes']['title']);
        $this->assertSame('Old Title', $activity->properties['old']['title']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $news = News::factory()->create();

        $initialCount = Activity::count();

        $news->update(['title' => $news->title]);

        $this->assertSame($initialCount, Activity::count());
    }
}
