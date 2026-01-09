<?php

namespace Modules\User\Tests\Feature\Database\Seeders;

use App\Models\User;
use Modules\React\Models\Follow;
use Modules\User\Database\Seeders\UserSeeder;
use Modules\User\Models\UserProfile;
use Modules\User\Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserSeederTest extends TestCase
{
    public function test_it_seeds_users(): void
    {
        User::truncate();
        UserProfile::truncate();
        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'User']);

        $this->seed(UserSeeder::class);

        $this->assertDatabaseCount(User::class, 6);

        $this->assertDatabaseCount(UserProfile::class, 5);

        $this->assertDatabaseCount(Follow::class, 15);

        $this->assertDatabaseHas(User::class, [
            'email'    => 'admin@example.com',
            'username' => 'admin',
        ]);
    }
}
