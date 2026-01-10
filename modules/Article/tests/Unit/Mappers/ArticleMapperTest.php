<?php

namespace Modules\Article\Tests\Unit\Mappers;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\Article\Mappers\ArticleMapper;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\User\Data\UserData;

class ArticleMapperTest extends TestCase
{
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

        $articleData = ArticleMapper::toData($article, $user);

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

        $articleData = ArticleMapper::toData($article, $user, true);

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

        $data = ArticleMapper::toData($article, $userData, true);

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

        $data = ArticleMapper::toData($article, $userData);

        $this->assertFalse($data->isLiked);
        $this->assertFalse($data->isDisliked);
    }
}
