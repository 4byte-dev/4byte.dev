<?php

namespace Modules\Tag\Tests\Feature\Database\Seeders;

use Modules\Tag\Database\Seeders\TagSeeder;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;
use Modules\Tag\Tests\TestCase;

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
