<?php

namespace Modules\User\Tests\Feature\Http\Requests\Auth;

use App\Models\User;
use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Modules\User\Tests\TestCase;

class RegisterRequestTest extends TestCase
{
    public function test_register_fails_when_register_disabled(): void
    {
        $this->mockSecuritySettings(registerEnabled: false);

        $this->postJson(route('api.auth.register'), $this->validData());

        $this->expectException(ValidationException::class);
    }

    public function test_register_validation_fails_for_invalid_password(): void
    {
        $request = $this->postJson(route('api.auth.register'), [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'username' => 'john',
            'password' => 'password',
        ]);

        $request->assertStatus(422);
        $this->assertArrayHasKey('password', $request->json('errors'));
    }

    public function test_register_validation_fails_for_invalid_username(): void
    {
        $request = $this->postJson(route('api.auth.register'), [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'username' => 'John_Doe',
            'password' => 'Password1!',
        ]);

        $request->assertStatus(422);
        $this->assertArrayHasKey('username', $request->json('errors'));
    }

    public function test_register_fails_when_email_is_not_unique(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $request = $this->postJson(route('api.auth.register'), [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'username' => 'john2',
            'password' => 'Password1!',
        ]);

        $request->assertStatus(422);
        $this->assertArrayHasKey('email', $request->json('errors'));
    }

    public function test_register_succeeds_with_valid_data(): void
    {
        Notification::fake();
        Event::fake();

        $this->mockSecuritySettings();

        Auth::shouldReceive('login')
            ->once()
            ->withArgs(fn ($user, $remember) => $remember === true);

        $request = $this->postJson(route('api.auth.register'), $this->validData());

        $this->assertEquals(200, $request->status());

        $this->assertDatabaseHas('users', [
            'email'    => 'john@example.com',
            'username' => 'john',
        ]);

        $user = User::where('email', 'john@example.com')->first();

        $this->assertTrue(
            Hash::check('Password1!', $user->password)
        );

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );

        Event::assertDispatched(Registered::class);
    }

    private function validData(): array
    {
        return [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'username' => 'john',
            'password' => 'Password1!',
        ];
    }

    private function mockSecuritySettings(bool $registerEnabled = true, int $maxAttempts = 5): void {
        $settings                        = new SecuritySettings();
        $settings->register_enabled      = $registerEnabled;
        $settings->max_register_attempts = $maxAttempts;

        $this->mock(SettingsService::class, function ($mock) use ($settings) {
            $mock->shouldReceive('getSecuritySettings')
                ->andReturn($settings);
        });
    }
}
