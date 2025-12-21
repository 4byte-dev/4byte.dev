<?php

namespace Modules\Article\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\Article\Models\Article;
use Modules\Article\Policies\ArticlePolicy;
use Modules\Article\Tests\TestCase;

class ArticlePolicyTest extends TestCase
{
    private ArticlePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ArticlePolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_article')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('view_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 1;

        $this->assertTrue($this->policy->view($user, $article));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('view_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 2;

        $this->assertFalse($this->policy->view($user, $article));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article = Mockery::mock(Article::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $article));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_article')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('update_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 1;

        $this->assertTrue($this->policy->update($user, $article));
    }

    public function test_update_others_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('update_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 2;

        $this->assertFalse($this->policy->update($user, $article));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article = Mockery::mock(Article::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $article));
    }

    public function test_delete_own_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('delete_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $article));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('delete_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $article));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article = Mockery::mock(Article::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $article));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $article));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $article));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article = Mockery::mock(Article::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $article));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('restore_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $article));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('restore_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $article));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article = Mockery::mock(Article::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $article));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $article));
    }

    public function test_replicate_others_article(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_article')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article          = Mockery::mock(Article::class)->makePartial();
        $article->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $article));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_article')->andReturn(true);

        /** @var Article|MockInterface $article */
        $article = Mockery::mock(Article::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $article));
    }
}
