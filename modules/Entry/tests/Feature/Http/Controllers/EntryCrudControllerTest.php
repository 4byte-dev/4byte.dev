<?php

namespace Modules\Entry\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Modules\Entry\Models\Entry;
use Modules\Entry\Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class EntryCrudControllerTest extends TestCase
{
    public function test_it_creates_entry_with_content_only(): void
    {
        Permission::firstOrCreate(['name' => 'create_entry']);

        $user = User::factory()->create();
        $user->givePermissionTo('create_entry');

        $this->actingAs($user);

        $payload = [
            'content' => 'This is a test entry content created to satisfy minimum length rules.',
        ];

        $response = $this->post(
            route('api.entry.crud.create'),
            $payload
        );

        $response->assertOk();
        $response->assertJsonStructure(['slug']);

        $slug = $response->json('slug');

        $this->assertTrue(Str::isUuid($slug));

        $this->assertDatabaseHas('entries', [
            'slug'    => $slug,
            'content' => 'This is a test entry content created to satisfy minimum length rules.',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_creates_entry_with_multiple_media_files(): void
    {
        Permission::firstOrCreate(['name' => 'create_entry']);

        $user = User::factory()->create();
        $user->givePermissionTo('create_entry');

        $this->actingAs($user);

        $media = [
            UploadedFile::fake()->image('image1.jpg'),
            UploadedFile::fake()->image('image2.jpg'),
        ];

        $payload = [
            'content' => 'Entry with media',
            'media'   => $media,
        ];

        $response = $this->post(
            route('api.entry.crud.create'),
            $payload
        );

        $response->assertOk();

        $entry = Entry::first();

        $this->assertNotNull($entry);
        $this->assertEquals('Entry with media', $entry->content);

        $this->assertCount(
            2,
            $entry->getMedia('content'),
            'Media files were not attached correctly'
        );
    }

    public function test_it_returns_403_if_user_cannot_create_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'content' => 'Unauthorized entry',
        ];

        $response = $this->postJson(
            route('api.entry.crud.create'),
            $payload
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertDatabaseMissing('entries', [
            'content' => 'Unauthorized entry',
        ]);
    }

    public function test_guest_cannot_create_entry(): void
    {
        $payload = [
            'content' => 'Guest entry',
        ];

        $response = $this->postJson(
            route('api.entry.crud.create'),
            $payload
        );

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_entry_slug_is_unique_for_each_creation(): void
    {
        Permission::firstOrCreate(['name' => 'create_entry']);

        $user = User::factory()->create();
        $user->givePermissionTo('create_entry');

        $this->actingAs($user);

        $payload = [
            'content' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
        ];

        $response1 = $this->postJson(route('api.entry.crud.create'), $payload);
        $response2 = $this->postJson(route('api.entry.crud.create'), $payload);

        $slug1 = $response1->json('slug');
        $slug2 = $response2->json('slug');

        $this->assertNotEquals($slug1, $slug2);
        $this->assertTrue(Str::isUuid($slug1));
        $this->assertTrue(Str::isUuid($slug2));
    }
}
