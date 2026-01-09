<?php

namespace Modules\User\Tests\Unit\Database;

use Modules\User\Models\UserProfile;
use Modules\User\Tests\TestCase;

class UserProfileFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_user_profile(): void
    {
        $profile = UserProfile::factory()->create();

        $this->assertInstanceOf(UserProfile::class, $profile);
        $this->assertNotNull($profile->role);
        $this->assertNotNull($profile->bio);
        $this->assertNotNull($profile->location);
        $this->assertNotNull($profile->website);
        $this->assertNotNull($profile->socials);
    }

    public function test_role_is_valid(): void
    {
        $profile = UserProfile::factory()->create();

        $this->assertContains(
            $profile->role,
            ['user', 'admin', 'moderator']
        );
    }

    public function test_socials_is_an_array(): void
    {
        $profile = UserProfile::factory()->create();

        $this->assertIsArray($profile->socials);
        $this->assertArrayHasKey('twitter', $profile->socials);
        $this->assertArrayHasKey('instagram', $profile->socials);
    }

    public function test_website_is_a_valid_url(): void
    {
        $profile = UserProfile::factory()->create();

        $this->assertTrue(
            filter_var($profile->website, FILTER_VALIDATE_URL) !== false
        );
    }

    public function test_factory_creates_multiple_profiles(): void
    {
        $profiles = UserProfile::factory()->count(5)->create();

        $this->assertCount(5, $profiles);
    }
}
