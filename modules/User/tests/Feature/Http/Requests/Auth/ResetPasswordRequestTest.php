<?php

namespace Modules\User\Tests\Feature\Http\Requests\Auth;

use App\Models\User;
use App\Services\SettingsService;
use App\Settings\SecuritySettings;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Modules\User\Http\Requests\Auth\ResetPasswordRequest;
use Modules\User\Tests\TestCase;

class ResetPasswordRequestTest extends TestCase
{
    protected function makeRequest(array $data): ResetPasswordRequest
    {
        $request = ResetPasswordRequest::create(
            '/reset-password',
            'POST',
            $data,
            [],
            [],
            [
                'REMOTE_ADDR'  => '127.0.0.1',
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT'  => 'application/json',
            ]
        );

        $request->setContainer(app());
        $request->setRedirector(app(Redirector::class));

        app()->instance(ResetPasswordRequest::class, $request);

        return $request;
    }

    public function test_reset_password_fails_when_disabled(): void
    {
        $this->mockSecuritySettings(passwordResetEnabled: false);

        $request = $this->makeRequest([
            'email'    => 'user@example.com',
            'token'    => 'token',
            'password' => 'Password1!',
        ]);

        $this->expectException(ValidationException::class);

        $request->resetPassword();
    }

    public function test_reset_password_is_rate_limited(): void
    {
        Event::fake();
        $this->mockSecuritySettings(maxAttempts: 1);

        $email = 'user@example.com';
        $key   = strtolower($email) . '|127.0.0.1';

        RateLimiter::hit($key, 60);
        RateLimiter::hit($key, 60);

        $request = $this->makeRequest([
            'email'    => $email,
            'token'    => 'token',
            'password' => 'Password1!',
        ]);

        try {
            $request->resetPassword();
            $this->fail('ValidationException expected');
        } catch (ValidationException) {
            Event::assertDispatched(Lockout::class);
        }
    }

    public function test_reset_password_fails_when_broker_returns_error(): void
    {
        $this->mockSecuritySettings();

        Password::shouldReceive('broker')
            ->andReturnSelf();

        Password::shouldReceive('reset')
            ->once()
            ->andReturn(Password::INVALID_TOKEN);

        $request = $this->makeRequest([
            'email'    => 'user@example.com',
            'token'    => 'invalid-token',
            'password' => 'Password1!',
        ]);

        $this->expectException(ValidationException::class);

        $request->resetPassword();
    }

    public function test_reset_password_succeeds(): void
    {
        Event::fake();
        $this->mockSecuritySettings();

        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => Hash::make('OldPassword1!'),
        ]);

        Password::shouldReceive('broker')
            ->once()
            ->andReturnSelf();

        Password::shouldReceive('reset')
            ->once()
            ->andReturnUsing(function ($data, $callback) use ($user) {
                $callback($user);

                return Password::PASSWORD_RESET;
            });

        $request = $this->makeRequest([
            'email'    => 'user@example.com',
            'token'    => 'valid-token',
            'password' => 'NewPassword1!',
        ]);

        $response = $request->resetPassword();

        $this->assertEquals(200, $response->getStatusCode());

        $user->refresh();

        $this->assertTrue(
            Hash::check('NewPassword1!', $user->password)
        );

        Event::assertDispatched(PasswordReset::class);
    }

    public function test_password_validation_fails_for_weak_password(): void
    {
        $this->mockSecuritySettings();

        $request = $this->makeRequest([
            'email'    => 'user@example.com',
            'token'    => 'token',
            'password' => 'password',
        ]);

        $this->expectException(ValidationException::class);

        $request->validateResolved();
    }

    public function test_throttle_key_contains_lowercase_email(): void
    {
        $this->mockSecuritySettings();

        $request = $this->makeRequest([
            'email'    => 'TEST@Example.COM',
            'token'    => 'token',
            'password' => 'Password1!',
        ]);

        try {
            $request->resetPassword();
        } catch (\Throwable) {
            // ignore
        }

        $key = 'test@example.com|127.0.0.1';

        $this->assertGreaterThan(
            0,
            RateLimiter::attempts($key)
        );
    }

    private function mockSecuritySettings(bool $passwordResetEnabled = true, int $maxAttempts = 5): void {
        $settings                              = new SecuritySettings();
        $settings->password_reset_enabled      = $passwordResetEnabled;
        $settings->max_reset_password_attempts = $maxAttempts;

        $this->mock(SettingsService::class, function ($mock) use ($settings) {
            $mock->shouldReceive('getSecuritySettings')
                ->andReturn($settings);
        });
    }
}
