<?php

namespace Modules\Category\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProfile;
use Modules\Category\Services\CategoryService;
use Modules\Category\Tests\TestCase;
use Modules\News\Enums\NewsStatus;
use Modules\News\Models\News;
use Modules\React\Services\ReactService;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;

class CategoryServiceTest extends TestCase
{
    protected CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(CategoryService::class);
    }

    public function test_it_can_get_category_data_by_id(): void
    {
        $category = Category::factory()->create();

        $data = $this->service->getData($category->id);

        $this->assertEquals($category->name, $data->name);
        $this->assertEquals($category->slug, $data->slug);
        $this->assertTrue(Cache::has("category:{$category->id}"));
    }

    public function test_it_can_get_category_id_by_slug(): void
    {
        $category = Category::factory()->create();

        $id = $this->service->getId($category->slug);

        $this->assertEquals($category->id, $id);
        $this->assertTrue(Cache::has("category:{$category->slug}:id"));
    }

    public function test_it_can_get_profile_data(): void
    {
        $category     = Category::factory()->create();
        $profile      = CategoryProfile::factory()->create(['category_id' => $category->id]);

        $data = $this->service->getProfileData($category->id);

        $this->assertEquals($profile->description, $data->description);
        $this->assertEquals($profile->color, $data->color);
        $this->assertTrue(Cache::has("category:{$category->id}:profile"));
    }

    public function test_it_can_count_articles(): void
    {
        $category = Category::factory()->create();

        $article = Article::factory()->create(['status' => ArticleStatus::PUBLISHED]);
        $article->categories()->attach($category);

        app(ReactService::class)->incrementCount(Category::class, $category->id, 'articles');

        $count = $this->service->getArticlesCount($category->id);

        $this->assertEquals(1, $count);
        $this->assertTrue(Cache::has("react:counts:category:{$category->id}:articles"));
    }

    public function test_it_can_count_news(): void
    {
        $category = Category::factory()->create();

        $news = News::factory()->create(['status' => NewsStatus::PUBLISHED]);
        $news->categories()->attach($category);

        app(ReactService::class)->incrementCount(Category::class, $category->id, 'news');

        $count = $this->service->getNewsCount($category->id);

        $this->assertEquals(1, $count);
        $this->assertTrue(Cache::has("react:counts:category:{$category->id}:news"));
    }

    public function test_it_can_list_tags_by_category(): void
    {
        $category = Category::factory()->create();

        $tagA     = Tag::factory()->create();
        $profileA = TagProfile::factory()->create(['tag_id' => $tagA->id]);
        $profileA->categories()->attach($category);

        $otherCategory = Category::factory()->create();
        $tagB          = Tag::factory()->create();
        $profileB      = TagProfile::factory()->create(['tag_id' => $tagB->id]);
        $profileB->categories()->attach($otherCategory);

        $tagC = Tag::factory()->create();
        TagProfile::factory()->create(['tag_id' => $tagC->id]);

        $tags = $this->service->listTags($category->id);

        $this->assertTrue(collect($tags)->contains('slug', $tagA->slug));
        $this->assertFalse(collect($tags)->contains('slug', $tagB->slug));
        $this->assertFalse(collect($tags)->contains('slug', $tagC->slug));

        $this->assertTrue(Cache::has("category:{$category->id}:tags"));
    }

    public function test_it_can_list_related_empty_when_no_profile(): void
    {
        $category = Category::factory()->create();

        $related = $this->service->listTags($category->id);

        $this->assertEmpty($related);
    }
}
