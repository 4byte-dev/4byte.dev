<?php

namespace App\Tests\Unit\Models;

use App\Models\User;
use Modules\User\Models\UserProfile;
use Modules\User\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

class UserTest extends TestCase
{
    public function test_it_can_be_created_with_factory(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->id);
    }

    public function test_it_has_profile_relation(): void
    {
        $user = User::factory()->create();

        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(UserProfile::class, $user->profile);
        $this->assertEquals($profile->id, $user->profile->id);
    }

    public function test_it_can_have_roles(): void
    {
        $user = User::factory()->create();
        $role = Role::create([
            'name'       => 'admin',
            'guard_name' => 'web',
        ]);

        $user->assignRole($role);

        $this->assertTrue($user->hasRole('admin'));
    }

    public function test_it_logs_activity_on_create(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertNotNull($activity);
        $this->assertSame('created', $activity->description);
        $this->assertSame('Test User', $activity->properties['attributes']['name']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $user->update([
            'name' => 'New Name',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame('New Name', $activity->properties['attributes']['name']);
        $this->assertSame('Old Name', $activity->properties['old']['name']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $user = User::factory()->create();

        $initialCount = Activity::count();

        $user->update([
            'name' => $user->name,
        ]);

        $this->assertSame($initialCount, Activity::count());
    }

    public function test_it_can_be_soft_deleted(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertSoftDeleted($user);
    }

    public function test_it_can_be_restored_after_soft_delete(): void
    {
        $user = User::factory()->create();

        $user->delete();
        $user->restore();

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'deleted_at' => null,
        ]);
    }
}
