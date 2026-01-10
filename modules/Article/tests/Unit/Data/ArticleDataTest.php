<?php

namespace Modules\Article\Tests\Unit\Data;

use Carbon\Carbon;
use Modules\Article\Data\ArticleData;
use Modules\Article\Tests\TestCase;
use Modules\Category\Data\CategoryData;
use Modules\Tag\Data\TagData;
use Modules\User\Data\UserData;

class ArticleDataTest extends TestCase
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

        $articleData = new ArticleData(
            id: 2,
            title: 'Test Article',
            slug: 'test-article',
            excerpt: 'Text excerpt',
            content: 'Test content',
            image: [
                'image'      => 'https://cdn.4byte.dev/logo.png',
                'responsive' => [],
                'srcset'     => '',
                'thumb'      => null,
            ],
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
            sources: [
                ['url' => 'https://4byte.dev', 'date' => now()->toString()],
            ],
            likes: 5,
            dislikes: 7,
            comments: 1,
            isLiked: false,
            isDisliked: true,
            isSaved: false,
            canUpdate: false,
            canDelete: false,
            published_at: now()
        );

        $this->assertSame(2, $articleData->id);
        $this->assertSame('Test Article', $articleData->title);
        $this->assertSame('test-article', $articleData->slug);
        $this->assertSame('Text excerpt', $articleData->excerpt);
        $this->assertSame('Test content', $articleData->content);

        $this->assertSame('https://cdn.4byte.dev/logo.png', $articleData->image['image']);

        $this->assertSame(5, $articleData->likes);
        $this->assertSame(7, $articleData->dislikes);
        $this->assertSame(1, $articleData->comments);

        $this->assertFalse($articleData->isLiked);
        $this->assertTrue($articleData->isDisliked);
        $this->assertFalse($articleData->isSaved);

        $this->assertFalse($articleData->canUpdate);
        $this->assertFalse($articleData->canDelete);

        $this->assertInstanceOf(Carbon::class, $articleData->published_at);
        $this->assertInstanceOf(UserData::class, $articleData->user);

        $this->assertSame(10, $articleData->user->id);
        $this->assertSame('Test User', $articleData->user->name);
        $this->assertSame('testuser', $articleData->user->username);
        $this->assertSame('', $articleData->user->avatar);

        $this->assertSame(10, $articleData->user->followers);
        $this->assertSame(3, $articleData->user->followings);
        $this->assertTrue($articleData->user->isFollowing);

        $this->assertInstanceOf(Carbon::class, $articleData->user->created_at);

        $this->assertCount(1, $articleData->categories);
        $this->assertSame('Category Test', $articleData->categories[0]->name);
        $this->assertSame('category-test', $articleData->categories[0]->slug);

        $this->assertCount(1, $articleData->tags);
        $this->assertSame('Tag Test', $articleData->tags[0]->name);
        $this->assertSame('tag-test', $articleData->tags[0]->slug);

        $this->assertCount(1, $articleData->sources);
        $this->assertSame('https://4byte.dev', $articleData->sources[0]['url']);
        $this->assertTrue($this->isValidUrl($articleData->sources[0]['url']));
        $this->assertTrue($this->isValidDate($articleData->sources[0]['date']));

        $this->assertSame('article', $articleData->type);
    }
}
