<?php

namespace Packages\Tag\Tests\Feature\Database\Seeders;

use Packages\Tag\Database\Seeders\TagSeeder;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;
use Packages\Tag\Tests\TestCase;

class TagSeederTest extends TestCase
{
    public function test_it_seeds_tags(): void
    {
        $this->seed(TagSeeder::class);

        $this->assertDatabaseCount(Tag::class, 10);

        $this->assertDatabaseCount(TagProfile::class, 10);

        Tag::all()->each(function (Tag $tag) {
            $this->assertNotNull(
                $tag->profile,
            );
        });

        TagProfile::all()->each(function (TagProfile $profile) {
            $this->assertDatabaseHas(Tag::class, [
                'id' => $profile->tag_id,
            ]);
        });

        $this->assertEquals(
            10,
            Tag::has('profile')->count(),
        );

        $this->assertEquals(
            Tag::count(),
            TagProfile::count()
        );
    }
}
