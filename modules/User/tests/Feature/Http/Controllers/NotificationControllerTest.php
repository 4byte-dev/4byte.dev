<?php

namespace Modules\User\Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Modules\User\Models\User;
use Modules\User\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_unread_notification_count(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            DatabaseNotification::create([
                'id'              => (string) Str::uuid(),
                'notifiable_id'   => $user->id,
                'notifiable_type' => $user::class,
                'read_at'         => null,
                'data'            => [],
                'type'            => 'TestNotification',
            ]);
        }

        DatabaseNotification::create([
            'id'              => (string) Str::uuid(),
            'notifiable_id'   => $user->id,
            'notifiable_type' => $user::class,
            'read_at'         => now(),
            'data'            => [],
            'type'            => 'TestNotification',
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.notifications.count'));

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'count' => 3,
            ]);
    }

    public function test_it_lists_latest_notifications(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 15; $i++) {
            DatabaseNotification::create([
                'id'              => (string) Str::uuid(),
                'notifiable_id'   => $user->id,
                'notifiable_type' => $user::class,
                'read_at'         => null,
                'data'            => [
                    'message' => "Notification {$i}",
                ],
                'type' => 'TestNotification',
            ]);
        }

        $response = $this
            ->actingAs($user)
            ->getJson(route('api.notifications.list'));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonCount(10);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'data',
                'read_at',
                'created_at',
            ],
        ]);
    }

    public function test_it_marks_single_notification_as_read(): void
    {
        $user = User::factory()->create();

        $notification = DatabaseNotification::create([
            'id'              => (string) Str::uuid(),
            'notifiable_id'   => $user->id,
            'notifiable_type' => $user::class,
            'read_at'         => null,
            'data'            => [],
            'type'            => 'TestNotification',
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('api.notifications.read'), [
                'id' => $notification->id,
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('notifications', [
            'id'      => $notification->id,
            'read_at' => null,
        ]);
    }

    public function test_it_marks_all_notifications_as_read(): void
    {
        $user = User::factory()->create();

        DatabaseNotification::create([
            'notifiable_id'   => $user->id,
            'notifiable_type' => $user::class,
            'read_at'         => null,
            'data'            => [],
            'type'            => 'TestNotification',
            'id'              => (string) Str::uuid(),
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson(route('api.notifications.read-all'));

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals(
            0,
            $user->fresh()->unreadNotifications()->count()
        );
    }
}
