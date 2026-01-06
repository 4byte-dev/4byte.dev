<?php

namespace Modules\Page\Tests\Unit\Policies;

use App\Models\User;
use Mockery;
use Mockery\MockInterface;
use Modules\Page\Models\Page;
use Modules\Page\Policies\PagePolicy;
use Modules\Page\Tests\TestCase;

class PagePolicyTest extends TestCase
{
    private PagePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PagePolicy();
    }

    public function test_view_any(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_page')->andReturn(true);
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_view_own_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('view_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 1;

        $this->assertTrue($this->policy->view($user, $page));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_others_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('view_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('view_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 2;

        $this->assertFalse($this->policy->view($user, $page));
        $this->assertFalse($this->policy->viewAny($user));
    }

    public function test_view_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('view_any_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page = Mockery::mock(Page::class)->makePartial();

        $this->assertTrue($this->policy->view($user, $page));
        $this->assertTrue($this->policy->viewAny($user));
    }

    public function test_create(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('create_page')->andReturn(true);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_update_own_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('update_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 1;

        $this->assertTrue($this->policy->update($user, $page));
    }

    public function test_update_others_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('update_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('update_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 2;

        $this->assertFalse($this->policy->update($user, $page));
    }

    public function test_update_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('update_any_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page = Mockery::mock(Page::class)->makePartial();

        $this->assertTrue($this->policy->update($user, $page));
    }

    public function test_delete_own_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('delete_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 1;

        $this->assertTrue($this->policy->delete($user, $page));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_others_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('delete_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('delete_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 2;

        $this->assertFalse($this->policy->delete($user, $page));
        $this->assertFalse($this->policy->deleteAny($user));
    }

    public function test_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('delete_any_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page = Mockery::mock(Page::class)->makePartial();

        $this->assertTrue($this->policy->delete($user, $page));
        $this->assertTrue($this->policy->deleteAny($user));
    }

    public function test_force_delete_own_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 1;

        $this->assertTrue($this->policy->forceDelete($user, $page));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_others_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('force_delete_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('force_delete_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 2;

        $this->assertFalse($this->policy->forceDelete($user, $page));
        $this->assertFalse($this->policy->forceDeleteAny($user));
    }

    public function test_force_delete_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('force_delete_any_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page = Mockery::mock(Page::class)->makePartial();

        $this->assertTrue($this->policy->forceDelete($user, $page));
        $this->assertTrue($this->policy->forceDeleteAny($user));
    }

    public function test_restore_own_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('restore_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 1;

        $this->assertTrue($this->policy->restore($user, $page));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_others_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('restore_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('restore_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 2;

        $this->assertFalse($this->policy->restore($user, $page));
        $this->assertFalse($this->policy->restoreAny($user));
    }

    public function test_restore_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('restore_any_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page = Mockery::mock(Page::class)->makePartial();

        $this->assertTrue($this->policy->restore($user, $page));
        $this->assertTrue($this->policy->restoreAny($user));
    }

    public function test_replicate_own_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 1;

        $this->assertTrue($this->policy->replicate($user, $page));
    }

    public function test_replicate_others_page(): void
    {
        /** @var User|MockInterface $user */
        $user     = Mockery::mock(User::class)->makePartial();
        $user->id = 1;
        $user->shouldReceive('can')->with('replicate_any_page')->andReturn(false);
        $user->shouldReceive('can')->with('replicate_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page          = Mockery::mock(Page::class)->makePartial();
        $page->user_id = 2;

        $this->assertFalse($this->policy->replicate($user, $page));
    }

    public function test_replicate_any_permission_overrides_ownership(): void
    {
        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('can')->with('replicate_any_page')->andReturn(true);

        /** @var Page|MockInterface $page */
        $page = Mockery::mock(Page::class)->makePartial();

        $this->assertTrue($this->policy->replicate($user, $page));
    }
}
