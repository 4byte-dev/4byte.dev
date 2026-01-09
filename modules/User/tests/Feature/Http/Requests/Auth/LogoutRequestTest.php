<?php

namespace Modules\User\Tests\Feature\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Modules\User\Tests\TestCase;

class LogoutRequestTest extends TestCase
{
    public function test_user_can_logout_successfully(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(
            route('api.auth.logout')
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Logout successful',
            ]);

        $this->assertGuest();

        Event::assertDispatched(Logout::class);
    }

    public function test_session_is_invalidated_and_token_regenerated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $oldSessionId = session()->getId();
        $oldToken     = session()->token();

        $this->postJson(route('api.auth.logout'));

        $this->assertFalse(session()->isStarted());

        $this->assertNotEquals(
            $oldSessionId,
            session()->getId()
        );

        $this->assertNotEquals(
            $oldToken,
            session()->token()
        );
    }
}
