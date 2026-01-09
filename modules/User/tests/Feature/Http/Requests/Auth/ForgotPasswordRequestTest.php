<?php

namespace Modules\User\Tests\Feature\Http\Requests\Auth;

use App\Models\User;
use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\ResetPassword;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Modules\User\Tests\TestCase;

class ForgotPasswordRequestTest extends TestCase
{
    public function test_password_reset_fails_when_disabled(): void
    {
        $this->mockSecuritySettings(passwordResetEnabled: false);

        $response = $this->postJson(
            route('api.auth.forgot-password'),
            ['email' => 'test@example.com']
        );

        $response->assertStatus(422);
    }

    public function test_request_is_rate_limited(): void
    {
        Event::fake();
        $this->mockSecuritySettings(maxAttempts: 1);

        $email = 'test@example.com';

        $throttleKey = strtolower($email) . '|' . request()->ip();

        RateLimiter::hit($throttleKey, 60);

        $response = $this->postJson(
            route('api.auth.forgot-password'),
            ['email' => $email]
        );

        $response->assertStatus(429);

        Event::assertDispatched(Lockout::class);
    }

    public function test_password_reset_link_is_sent_successfully(): void
    {
        Notification::fake();
        Event::fake();

        $this->mockSecuritySettings();

        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        Password::shouldReceive('broker')
            ->once()
            ->with(Filament::getAuthPasswordBroker())
            ->andReturnSelf();

        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturnUsing(function ($credentials, $callback) use ($user) {
                $callback($user, 'fake-token');

                return Password::RESET_LINK_SENT;
            });

        $response = $this->postJson(
            route('api.auth.forgot-password'),
            ['email' => 'user@example.com']
        );

        $response->assertOk();

        Notification::assertSentTo(
            $user,
            ResetPassword::class
        );

        Event::assertDispatched(PasswordResetLinkSent::class);
    }

    public function test_throttle_key_uses_lowercase_email(): void
    {
        Event::fake();
        $this->mockSecuritySettings();

        $this->postJson(
            route('api.auth.forgot-password'),
            ['email' => 'TEST@Example.COM']
        );

        $throttleKey = strtolower('test@example.com') . '|' . request()->ip();

        $this->assertTrue(
            RateLimiter::attempts($throttleKey) > 0
        );
    }

    private function mockSecuritySettings(bool $passwordResetEnabled = true, int $maxAttempts = 5): void {
        $settings                                = new SecuritySettings();
        $settings->password_reset_enabled        = $passwordResetEnabled;
        $settings->max_reset_password_attempts   = $maxAttempts;

        $this->mock(SettingsService::class, function ($mock) use ($settings) {
            $mock->shouldReceive('getSecuritySettings')
                ->andReturn($settings);
        });
    }
}
