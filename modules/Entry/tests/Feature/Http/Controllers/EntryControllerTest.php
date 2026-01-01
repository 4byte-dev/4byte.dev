<?php

namespace Modules\Entry\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Entry\Data\EntryData;
use Modules\Entry\Services\EntryService;
use Modules\Entry\Tests\TestCase;
use Modules\User\Data\UserData;
use Symfony\Component\HttpFoundation\Response;

class EntryControllerTest extends TestCase
{
    public function test_it_displays_entry_detail_page(): void
    {
        $entryId = 1;
        $slug    = 'test-entry';

        $userData = new UserData(
            id: 10,
            name: 'Test User',
            username: 'testuser',
            avatar: '',
            followers: 15,
            followings: 5,
            isFollowing: true,
            created_at: now()
        );

        $entryData = new EntryData(
            id: $entryId,
            slug: $slug,
            content: 'Test entry content',
            media: [
                [
                    'image'      => 'https://cdn.4byte.dev/entry.png',
                    'responsive' => [],
                    'srcset'     => '',
                ],
            ],
            user: $userData,
            likes: 12,
            dislikes: 3,
            comments: 4,
            isLiked: true,
            isDisliked: false,
            isSaved: true,
            canUpdate: false,
            canDelete: false,
            published_at: now(),
            type: 'entry'
        );

        $entryService = Mockery::mock(EntryService::class);
        $entryService->shouldReceive('getId')
            ->once()
            ->with($slug)
            ->andReturn($entryId);

        $entryService->shouldReceive('getData')
            ->once()
            ->with($entryId)
            ->andReturn($entryData);

        $this->app->instance(EntryService::class, $entryService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')
            ->once()
            ->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getEntrySEO')
            ->once()
            ->with($entryData, $userData)
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('entry.view', $slug));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Entry/Detail')

                ->has('entry')

                ->where('entry.id', $entryId)
                ->where('entry.slug', $slug)
                ->where('entry.content', 'Test entry content')
                ->where('entry.type', 'entry')

                ->has('entry.media', 1)
                ->where('entry.media.0.image', 'https://cdn.4byte.dev/entry.png')

                ->where('entry.user.id', 10)
                ->where('entry.user.name', 'Test User')
                ->where('entry.user.username', 'testuser')
                ->where('entry.user.followers', 15)
                ->where('entry.user.followings', 5)
                ->where('entry.user.isFollowing', true)

                ->where('entry.likes', 12)
                ->where('entry.dislikes', 3)
                ->where('entry.comments', 4)

                ->where('entry.isLiked', true)
                ->where('entry.isDisliked', false)
                ->where('entry.isSaved', true)

                ->where('entry.canUpdate', false)
                ->where('entry.canDelete', false)

                ->where('entry.published_at', fn ($date) => $this->isValidDate($date))
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
