<?php

namespace Modules\Page\Tests\Unit\Data;

use App\Models\User;
use Carbon\Carbon;
use Mockery;
use Mockery\MockInterface;
use Modules\Page\Data\PageData;
use Modules\Page\Models\Page;
use Modules\Page\Tests\TestCase;
use Modules\User\Data\UserData;

class PageDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $userData = new UserData(
            id: 10,
            name: 'Test User',
            username: 'testuser',
            avatar: '',
            followers: 5,
            followings: 2,
            isFollowing: true,
            created_at: now()
        );

        $pageData = new PageData(
            id: 2,
            title: 'Test Page',
            slug: 'test-page',
            content: 'Test content',
            excerpt: 'Test excerpt',
            image: [
                'image'      => 'https://cdn.4byte.dev/logo.png',
                'responsive' => [],
                'srcset'     => '',
                'thumb'      => 'https://cdn.4byte.dev/thumb.png',
            ],
            user: $userData,
            canUpdate: false,
            canDelete: false,
            published_at: now()
        );

        $this->assertSame(2, $pageData->id);
        $this->assertSame('Test Page', $pageData->title);
        $this->assertSame('test-page', $pageData->slug);
        $this->assertSame('Test content', $pageData->content);
        $this->assertSame('Test excerpt', $pageData->excerpt);

        $this->assertSame(
            'https://cdn.4byte.dev/logo.png',
            $pageData->image['image']
        );

        $this->assertFalse($pageData->canUpdate);
        $this->assertFalse($pageData->canDelete);

        $this->assertInstanceOf(Carbon::class, $pageData->published_at);
        $this->assertInstanceOf(UserData::class, $pageData->user);

        $this->assertSame(10, $pageData->user->id);
        $this->assertSame('Test User', $pageData->user->name);
        $this->assertSame('testuser', $pageData->user->username);

        $this->assertSame('page', $pageData->type);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $page = Page::factory()->create([
            'title'   => 'Test Page',
            'slug'    => 'test-page',
            'content' => 'Page Content',
            'excerpt' => 'Page Excerpt',
        ]);

        $user = User::factory()->create([
            'name'     => 'User Name',
            'username' => 'username',
        ]);

        $userData = UserData::fromModel($user);

        $pageData = PageData::fromModel($page, $userData);

        $this->assertSame(0, $pageData->id);
        $this->assertSame('Test Page', $pageData->title);
        $this->assertSame('test-page', $pageData->slug);
        $this->assertSame('Page Content', $pageData->content);
        $this->assertSame('Page Excerpt', $pageData->excerpt);

        $this->assertInstanceOf(UserData::class, $pageData->user);
        $this->assertSame($user->name, $pageData->user->name);
        $this->assertSame($user->username, $pageData->user->username);

        $this->assertFalse($pageData->canUpdate);
        $this->assertFalse($pageData->canDelete);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $page = Page::factory()->create();

        $user     = User::factory()->create();
        $userData = UserData::fromModel($user);

        $pageData = PageData::fromModel($page, $userData, true);

        $this->assertSame($page->id, $pageData->id);
    }

    public function test_it_uses_gate_permissions_for_update_and_delete(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $userData = UserData::fromModel($user);

        /** @var Page|MockInterface $page */
        $page        = Mockery::mock(Page::class)->makePartial();
        $page->id    = 5;
        $page->title = 'Permission Page';
        $page->slug  = 'permission-page';

        $data = PageData::fromModel($page, $userData, true);

        $this->assertSame(5, $data->id);
        $this->assertFalse($data->canUpdate);
        $this->assertFalse($data->canDelete);
    }
}
