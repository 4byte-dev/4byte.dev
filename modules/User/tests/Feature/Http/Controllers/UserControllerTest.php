<?php

namespace Modules\User\Tests\Feature\Http\Controllers;

use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\User\Models\User;
use Modules\User\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_user_profile_page(): void
    {
        $user = User::factory()->create([
            'username' => 'testuser',
        ]);

        $user->profile()->create([
            'role'     => 'Developer',
            'bio'      => 'Test bio',
            'location' => 'Test City',
            'website'  => 'https://example.com',
            'socials'  => ['twitter' => 'https://twitter.com/previewuser'],
        ]);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getUserSEO')
            ->once()
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('user.view', $user->username));

        $response->assertStatus(HttpResponse::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('User/Profile')
                ->has('user')
                ->has('profile')
                ->where('user.username', 'testuser')
                ->where('profile.role', 'Developer')
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }

    public function test_it_returns_user_profile_preview(): void
    {
        $user = User::factory()->create([
            'username' => 'previewuser',
        ]);

        $user->profile()->create([
            'role'     => 'Tester',
            'bio'      => 'Preview bio',
            'location' => 'Test City',
            'website'  => 'https://example.com',
            'socials'  => ['twitter' => 'https://twitter.com/previewuser'],

        ]);

        $response = $this->getJson(route('api.user.preview', $user->username));

        $response->assertStatus(HttpResponse::HTTP_OK);

        $response->assertJsonStructure([
            'user',
            'profile',
        ]);
    }

    public function test_it_displays_user_settings_page(): void
    {
        $user = User::factory()->create();

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');

        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getUserSettingsSEO')
            ->once()
            ->andReturn($metadata);

        $this->app->instance(SeoService::class, $seoService);

        $response = $this
            ->actingAs($user)
            ->get(route('user.settings.view'));

        $response->assertStatus(HttpResponse::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('User/Settings')
                ->has('account')
                ->has('profile')
                ->has('sessions')
                ->where('account.email', $user->email)
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }

    public function test_it_updates_user_account(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('api.user.update.account'), [
                'name' => 'New Name',
            ]);

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'name' => 'New Name',
        ]);
    }

    public function test_it_updates_user_password_and_logs_out(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('api.user.update.password'), [
                'current_password'          => 'old-password',
                'new_password'              => 'new-password',
                'new_password_confirmation' => 'new-password',
            ]);

        $response->assertStatus(HttpResponse::HTTP_OK);

        $this->assertTrue(
            Hash::check('new-password', $user->fresh()->password)
        );
    }

    public function test_it_fails_when_current_password_is_invalid(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('correct-password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('api.user.update.password'), [
                'current_password'          => 'wrong-password',
                'new_password'              => 'new-password',
                'new_password_confirmation' => 'new-password',
            ]);

        $response->assertStatus(HttpResponse::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors(['current_password']);
    }
}
