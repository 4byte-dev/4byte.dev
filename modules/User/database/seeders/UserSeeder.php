<?php

namespace Modules\User\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\React\Models\Follow;
use Modules\User\Models\UserProfile;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::withoutEvents(function () {

            $adminUser = User::create([
                'name'     => 'Admin',
                'username' => 'admin',
                'email'    => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);

            $adminUser->syncRoles(Role::where('name', 'Super Admin')->first());

            $userRole = Role::where('name', 'User')->first();

            User::factory()
                ->count(5)
                ->has(UserProfile::factory()->count(1), 'profile')
                ->has(Follow::factory()->count(3), 'followers')
                ->create()
                ->each(function (User $user) use ($userRole) {
                    $user->assignRole($userRole);
                });
        });
    }
}
