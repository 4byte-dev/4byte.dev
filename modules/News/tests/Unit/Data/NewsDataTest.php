<?php

namespace Modules\News\Tests\Unit\Data;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Modules\Category\Data\CategoryData;
use Modules\News\Data\NewsData;
use Modules\News\Mappers\NewsMapper;
use Modules\News\Models\News;
use Modules\News\Tests\TestCase;
use Modules\Tag\Data\TagData;
use Modules\User\Data\UserData;

class NewsDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $userData = new UserData(
            id: 10,
            name: 'Test User',
            username: 'testuser',
            avatar: '',
            followers: 10,
            followings: 3,
            isFollowing: true,
            created_at: now()
        );

        $newsData = new NewsData(
            id: 2,
            title: 'Test News',
            slug: 'test-news',
            content: 'News content',
            excerpt: 'News excerpt',
            image: [
                'image'      => 'https://cdn.4byte.dev/news.png',
                'responsive' => [],
                'srcset'     => '',
                'thumb'      => null,
            ],
            published_at: now(),
            user: $userData,
            categories: [
                new CategoryData(
                    id: 3,
                    name: 'Category Test',
                    slug: 'category-test',
                    followers: 10,
                    isFollowing: true
                ),
            ],
            tags: [
                new TagData(
                    id: 2,
                    name: 'Tag Test',
                    slug: 'tag-test',
                    followers: 3,
                    isFollowing: false
                ),
            ],
            canUpdate: false,
            canDelete: false
        );

        $this->assertSame(2, $newsData->id);
        $this->assertSame('Test News', $newsData->title);
        $this->assertSame('test-news', $newsData->slug);
        $this->assertSame('News content', $newsData->content);
        $this->assertSame('News excerpt', $newsData->excerpt);

        $this->assertSame('https://cdn.4byte.dev/news.png', $newsData->image['image']);

        $this->assertFalse($newsData->canUpdate);
        $this->assertFalse($newsData->canDelete);

        $this->assertInstanceOf(Carbon::class, $newsData->published_at);
        $this->assertInstanceOf(UserData::class, $newsData->user);
        $this->assertSame(10, $newsData->user->id);

        $this->assertCount(1, $newsData->categories);
        $this->assertSame('Category Test', $newsData->categories[0]->name);

        $this->assertCount(1, $newsData->tags);
        $this->assertSame('Tag Test', $newsData->tags[0]->name);

        $this->assertSame('news', $newsData->type);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $news = News::factory()->create([
            'title'   => 'Test News',
            'slug'    => 'test-slug',
            'excerpt' => 'Test Excerpt',
            'content' => 'Test Content',
        ]);

        $user = User::factory()->create([
            'name'     => 'User Name',
            'username' => 'username',
        ]);

        $userData = UserData::fromModel($user);

        $newsData = NewsMapper::toData($news, $userData);

        $this->assertSame(0, $newsData->id);
        $this->assertSame('Test News', $newsData->title);
        $this->assertSame('test-slug', $newsData->slug);
        $this->assertSame('Test Excerpt', $newsData->excerpt);
        $this->assertSame('Test Content', $newsData->content);
        $this->assertInstanceOf(UserData::class, $newsData->user);
        $this->assertSame($user->name, $newsData->user->name);
        $this->assertSame($user->username, $newsData->user->username);
        $this->assertFalse($newsData->canUpdate);
        $this->assertFalse($newsData->canDelete);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $news = News::factory()->create();

        $user = User::factory()->create();

        $userData = UserData::fromModel($user);

        $newsData = NewsMapper::toData($news, $userData, true);

        $this->assertSame($news->id, $newsData->id);
    }

    public function test_it_respects_gate_permissions(): void
    {
        $news = News::factory()->create();
        $user = User::factory()->create();

        $userData = UserData::fromModel($user);

        Gate::shouldReceive('allows')
            ->with('update', $news)
            ->andReturn(true);

        Gate::shouldReceive('allows')
            ->with('delete', $news)
            ->andReturn(false);

        $newsData = NewsMapper::toData($news, $userData, true);

        $this->assertTrue($newsData->canUpdate);
        $this->assertFalse($newsData->canDelete);
    }
}
