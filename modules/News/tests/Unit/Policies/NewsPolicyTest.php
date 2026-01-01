<?php

namespace Modules\News\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\News\Models\News;
use Modules\News\Policies\NewsPolicy;
use Modules\News\Tests\TestCase;

class NewsPolicyTest extends TestCase
{
    private NewsPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new NewsPolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_news')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('view_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 1;

        $this->assertTrue($this->policy->view($user, $news));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('view_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 2;

        $this->assertFalse($this->policy->view($user, $news));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news = Mockery::mock(News::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $news));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_news')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('update_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 1;

        $this->assertTrue($this->policy->update($user, $news));
    }

    public function test_update_others_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('update_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 2;

        $this->assertFalse($this->policy->update($user, $news));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news = Mockery::mock(News::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $news));
    }

    public function test_delete_own_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('delete_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $news));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('delete_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $news));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news = Mockery::mock(News::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $news));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $news));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $news));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news = Mockery::mock(News::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $news));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('restore_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $news));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('restore_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $news));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news = Mockery::mock(News::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $news));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $news));
    }

    public function test_replicate_others_news(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_news')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news          = Mockery::mock(News::class)->makePartial();
        $news->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $news));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_news')->andReturn(true);

        /** @var News|MockInterface $news */
        $news = Mockery::mock(News::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $news));
    }
}
