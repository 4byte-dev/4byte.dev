<?php

namespace Packages\Article\Tests\Unit\Data;

use App\Data\UserData;
use App\Models\User;
use Carbon\Carbon;
use Mockery;
use Mockery\MockInterface;
use Packages\Article\Data\ArticleData;
use Packages\Article\Models\Article;
use Packages\Article\Tests\TestCase;
use Packages\Category\Data\CategoryData;
use Packages\Tag\Data\TagData;

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

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $article = Article::factory()->create([
            'title'   => 'Test Article',
            'slug'    => 'test-slug',
            'excerpt' => 'Test Excerpt',
            'content' => 'Test Content',
        ]);

        $user = User::factory()->create([
            'name'     => 'User Name',
            'username' => 'username',
        ]);

        $user = UserData::fromModel($user);

        $articleData = ArticleData::fromModel($article, $user);

        $this->assertSame(0, $articleData->id);

        $this->assertSame('Test Article', $articleData->title);
        $this->assertSame('test-slug', $articleData->slug);
        $this->assertSame('Test Excerpt', $articleData->excerpt);
        $this->assertSame('Test Content', $articleData->content);

        $this->assertInstanceOf(UserData::class, $articleData->user);
        $this->assertSame($user->id, $articleData->user->id);
        $this->assertSame('User Name', $articleData->user->name);
        $this->assertSame('username', $articleData->user->username);

        $this->assertSame(0, $articleData->likes);
        $this->assertSame(0, $articleData->dislikes);
        $this->assertSame(0, $articleData->comments);

        $this->assertFalse($articleData->isLiked);
        $this->assertFalse($articleData->isDisliked);
        $this->assertFalse($articleData->isSaved);

        $this->assertFalse($articleData->canUpdate);
        $this->assertFalse($articleData->canDelete);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $article = Article::factory()->create();

        $user = User::factory()->create();

        $user = UserData::fromModel($user);

        $articleData = ArticleData::fromModel($article, $user, true);

        $this->assertSame($article->id, $articleData->id);
    }

    public function test_it_uses_model_methods_for_followers_and_follow_state(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $userData = UserData::fromModel($user);

        /** @var Article|MockInterface $article */
        $article        = Mockery::mock(Article::class)->makePartial();
        $article->id    = 10;
        $article->title = 'Test Article';
        $article->slug  = 'test-article';

        $article->setRelation('categories', collect());
        $article->setRelation('tags', collect());

        $article->shouldReceive('likesCount')
            ->once()
            ->andReturn(15);

        $article->shouldReceive('dislikesCount')
            ->once()
            ->andReturn(3);

        $article->shouldReceive('isLikedBy')
            ->once()
            ->with($user->id)
            ->andReturn(true);

        $article->shouldReceive('isDislikedBy')
            ->once()
            ->with($user->id)
            ->andReturn(false);

        $data = ArticleData::fromModel($article, $userData, true);

        $this->assertSame(10, $data->id);
        $this->assertSame(15, $data->likes);
        $this->assertSame(3, $data->dislikes);
        $this->assertTrue($data->isLiked);
        $this->assertFalse($data->isDisliked);
    }

    public function test_it_sets_like_and_dislike_state_as_false_for_guest_user(): void
    {
        $user = User::factory()->create();

        $userData = UserData::fromModel($user);

        /** @var Article|MockInterface $article */
        $article        = Mockery::mock(Article::class)->makePartial();
        $article->id    = 1;
        $article->title = 'Guest Article';
        $article->slug  = 'guest-article';

        $article->setRelation('categories', collect());
        $article->setRelation('tags', collect());

        $article->shouldReceive('likesCount')
            ->once()
            ->andReturn(0);

        $article->shouldReceive('dislikesCount')
            ->once()
            ->andReturn(0);

        $article->shouldReceive('isLikedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $article->shouldReceive('isDislikedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $data = ArticleData::fromModel($article, $userData);

        $this->assertFalse($data->isLiked);
        $this->assertFalse($data->isDisliked);
    }
}
