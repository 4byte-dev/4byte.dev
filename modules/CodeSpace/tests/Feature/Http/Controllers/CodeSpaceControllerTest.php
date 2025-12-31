<?php

namespace Modules\CodeSpace\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\CodeSpace\Data\CodeSpaceData;
use Modules\CodeSpace\Services\CodeSpaceService;
use Modules\CodeSpace\Tests\TestCase;
use Modules\User\Data\UserData;
use Symfony\Component\HttpFoundation\Response;

class CodeSpaceControllerTest extends TestCase
{
    public function test_it_displays_codespace_detail_page(): void
    {
        $codeSpaceId = 1;
        $slug        = 'test-codespace';

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

        $codeSpaceData = new CodeSpaceData(
            id: $codeSpaceId,
            name: 'Test CodeSpace',
            slug: $slug,
            files: [
                "index" => ['name' => 'index.js', 'language' => 'javascript', 'content' => 'console.log("hello");'],
            ],
            user: $userData,
            updated_at: now()
        );

        $codeSpaceService = Mockery::mock(CodeSpaceService::class);
        $codeSpaceService->shouldReceive('getId')
            ->once()
            ->with($slug)
            ->andReturn($codeSpaceId);

        $codeSpaceService->shouldReceive('getData')
            ->once()
            ->with($codeSpaceId)
            ->andReturn($codeSpaceData);

        $this->app->instance(CodeSpaceService::class, $codeSpaceService);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getCodeSpaceSeo')
            ->once()
            ->with($codeSpaceData, $codeSpaceData->user)
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('codespace.view', $slug));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('CodeSpace/Detail')
                ->has('codeSpace')
                ->where('codeSpace.id', $codeSpaceId)
                ->where('codeSpace.name', 'Test CodeSpace')
                ->where('codeSpace.slug', $slug)
                ->has('codeSpace.files', 1)
                ->where('codeSpace.files.index.name', 'index.js')
                ->where('codeSpace.files.index.language', 'javascript')
                ->where('codeSpace.files.index.content', 'console.log("hello");')
                ->where('codeSpace.user.id', 10)
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }
}
