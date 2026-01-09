<?php

namespace Modules\User\Tests\Unit\Data;

use Modules\User\Data\UserProfileData;
use Modules\User\Models\UserProfile;
use Modules\User\Tests\TestCase;

class UserProfileDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $profileData = new UserProfileData(
            id: 5,
            role: 'admin',
            bio: 'This is a test bio',
            location: 'Istanbul',
            website: 'https://4byte.dev',
            socials: [
                'https://twitter.com/test',
                'https://github.com/test',
            ],
            cover: [
                'image'      => 'https://cdn.4byte.dev/cover.jpg',
                'responsive' => [],
                'srcset'     => '',
            ]
        );

        $this->assertSame(5, $profileData->id);
        $this->assertSame('admin', $profileData->role);
        $this->assertSame('This is a test bio', $profileData->bio);
        $this->assertSame('Istanbul', $profileData->location);
        $this->assertSame('https://4byte.dev', $profileData->website);

        $this->assertIsArray($profileData->socials);
        $this->assertCount(2, $profileData->socials);
        $this->assertSame('https://twitter.com/test', $profileData->socials[0]);

        $this->assertIsArray($profileData->cover);
        $this->assertSame('https://cdn.4byte.dev/cover.jpg', $profileData->cover['image']);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $profile = UserProfile::factory()->create([
            'role'     => 'editor',
            'bio'      => 'Editor bio',
            'location' => 'Ankara',
            'website'  => 'https://example.com',
            'socials'  => ['https://linkedin.com/in/test'],
        ]);

        $data = UserProfileData::fromModel($profile);

        $this->assertSame(0, $data->id);
        $this->assertSame('editor', $data->role);
        $this->assertSame('Editor bio', $data->bio);
        $this->assertSame('Ankara', $data->location);
        $this->assertSame('https://example.com', $data->website);

        $this->assertSame($profile->socials, $data->socials);
        $this->assertIsArray($data->cover);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $profile = UserProfile::factory()->create();

        $data = UserProfileData::fromModel($profile, true);

        $this->assertSame($profile->id, $data->id);
    }
}
