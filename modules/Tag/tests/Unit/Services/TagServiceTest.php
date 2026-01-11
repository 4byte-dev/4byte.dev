<?php

namespace Modules\Tag\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Modules\News\Enums\NewsStatus;
use Modules\News\Models\News;
use Modules\React\Services\ReactService;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;
use Modules\Tag\Services\TagService;
use Modules\Tag\Tests\TestCase;

class TagServiceTest extends TestCase
{
    protected TagService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(TagService::class);
    }

    public function test_it_can_get_tag_data_by_id(): void
    {
        $tag = Tag::factory()->create();

        $data = $this->service->getData($tag->id);

        $this->assertEquals($tag->name, $data->name);
        $this->assertEquals($tag->slug, $data->slug);
        $this->assertTrue(Cache::has("tag:{$tag->id}"));
    }

    public function test_it_can_get_tag_id_by_slug(): void
    {
        $tag = Tag::factory()->create();

        $id = $this->service->getId($tag->slug);

        $this->assertEquals($tag->id, $id);
        $this->assertTrue(Cache::has("tag:{$tag->slug}:id"));
    }

    public function test_it_can_get_profile_data(): void
    {
        $tag     = Tag::factory()->create();
        $profile = TagProfile::factory()->create(['tag_id' => $tag->id]);

        $data = $this->service->getProfileData($tag->id);

        $this->assertEquals($profile->description, $data->description);
        $this->assertEquals($profile->color, $data->color);
        $this->assertTrue(Cache::has("tag:{$tag->id}:profile"));
    }

    public function test_it_can_count_articles(): void
    {
        $tag = Tag::factory()->create();

        $article = Article::factory()->create(['status' => ArticleStatus::PUBLISHED]);
        $article->tags()->attach($tag);

        app(ReactService::class)->incrementCount(Tag::class, $tag->id, 'articles');

        $count = $this->service->getArticlesCount($tag->id);

        $this->assertEquals(1, $count);
        $this->assertTrue(Cache::has("react:counts:tag:{$tag->id}:articles"));
    }

    public function test_it_can_count_news(): void
    {
        $tag = Tag::factory()->create();

        $news = News::factory()->create(['status' => NewsStatus::PUBLISHED]);
        $news->tags()->attach($tag);

        app(ReactService::class)->incrementCount(Tag::class, $tag->id, 'news');

        $count = $this->service->getNewsCount($tag->id);

        $this->assertEquals(1, $count);
        $this->assertTrue(Cache::has("react:counts:tag:{$tag->id}:news"));
    }

    public function test_it_can_list_related_tags(): void
    {
        $category = Category::factory()->create();

        $tagA     = Tag::factory()->create();
        $profileA = TagProfile::factory()->create(['tag_id' => $tagA->id]);
        $profileA->categories()->attach($category);

        $tagB     = Tag::factory()->create();
        TagProfile::factory()->withCategory($category)->create(['tag_id' => $tagB->id]);

        $tagC     = Tag::factory()->create();
        TagProfile::factory()->create(['tag_id' => $tagC->id]);

        $related = $this->service->listRelated($tagA->id);

        $this->assertTrue(collect($related)->contains('slug', $tagB->slug));
        $this->assertFalse(collect($related)->contains('slug', $tagA->slug));
        $this->assertFalse(collect($related)->contains('slug', $tagC->slug));
        $this->assertTrue(Cache::has("tag:{$tagA->id}:related"));
    }

    public function test_it_can_list_related_empty_when_no_profile(): void
    {
        $tag = Tag::factory()->create();

        $related = $this->service->listRelated($tag->id);

        $this->assertEmpty($related);
    }
}
