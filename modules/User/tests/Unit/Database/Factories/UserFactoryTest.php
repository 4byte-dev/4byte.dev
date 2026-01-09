<?php

namespace Modules\User\Tests\Unit\Database;

use App\Models\User;
use Modules\User\Tests\TestCase;

class UserFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_user(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->username);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }

    public function test_user_has_a_valid_email(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(
            filter_var($user->email, FILTER_VALIDATE_EMAIL) !== false
        );
    }

    public function test_user_password_is_hashed(): void
    {
        $user = User::factory()->create();

        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(password_verify('password', $user->password));
    }

    public function test_factory_creates_unique_users(): void
    {
        $users = User::factory()->count(10)->create();

        $this->assertCount(
            10,
            $users->pluck('email')->unique()
        );

        $this->assertCount(
            10,
            $users->pluck('username')->unique()
        );
    }

    public function test_unverified_state_sets_email_verified_at_to_null(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertNull($user->email_verified_at);
    }
}
