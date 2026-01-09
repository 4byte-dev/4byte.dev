<?php

namespace Modules\User\Tests\Feature\Http\Requests\Auth;

use App\Models\User;
use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Modules\User\Tests\TestCase;

class LoginRequestTest extends TestCase
{
    public function test_login_fails_when_login_disabled(): void
    {
        $this->mockSecuritySettings(loginEnabled: false);

        $response = $this->postJson(
            route('api.auth.login'),
            [
                'email'    => 'test@example.com',
                'password' => 'Password1!',
            ]
        );

        $response->assertStatus(422);
    }

    public function test_login_is_rate_limited(): void
    {
        Event::fake();
        $this->mockSecuritySettings(maxAttempts: 1);

        $email       = 'test@example.com';
        $throttleKey = strtolower($email) . '|' . request()->ip();

        RateLimiter::hit($throttleKey, 60);

        $response = $this->postJson(
            route('api.auth.login'),
            [
                'email'    => $email,
                'password' => 'Password1!',
            ]
        );

        $response->assertStatus(429);

        Event::assertDispatched(Lockout::class);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->mockSecuritySettings();

        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('Password1!'),
        ]);

        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(false);

        $response = $this->postJson(
            route('api.auth.login'),
            [
                'email'    => 'user@example.com',
                'password' => 'WrongPass1!',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_login_succeeds_with_valid_credentials(): void
    {
        Event::fake();
        RateLimiter::clear('*');
        $this->mockSecuritySettings();

        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('Password1!'),
        ]);

        Auth::shouldReceive('attempt')
            ->once()
            ->andReturn(true);

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $response = $this->postJson(
            route('api.auth.login'),
            [
                'email'    => 'user@example.com',
                'password' => 'Password1!',
            ]
        );

        $response->assertOk();

        Event::assertDispatched(Login::class);
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_must_match_regex(): void
    {
        $response = $this->postJson(
            route('api.auth.login'),
            [
                'email'    => 'test@example.com',
                'password' => 'password',
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_throttle_key_uses_lowercase_email(): void
    {
        Event::fake();
        $this->mockSecuritySettings();

        $this->postJson(
            route('api.auth.login'),
            [
                'email'    => 'TEST@Example.COM',
                'password' => 'Password1!',
            ]
        );

        $throttleKey = strtolower('test@example.com') . '|' . request()->ip();

        $this->assertTrue(
            RateLimiter::attempts($throttleKey) > 0
        );
    }

    private function mockSecuritySettings(bool $loginEnabled = true, int $maxAttempts = 5, int $seconds = 60): void {
        $settings                               = new SecuritySettings();
        $settings->login_enabled                = $loginEnabled;
        $settings->max_login_attempts           = $maxAttempts;
        $settings->max_login_attempts_seconds   = $seconds;

        $this->mock(SettingsService::class, function ($mock) use ($settings) {
            $mock->shouldReceive('getSecuritySettings')
                ->andReturn($settings);
        });
    }
}
