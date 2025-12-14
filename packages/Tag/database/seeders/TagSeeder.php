<?php

namespace Packages\Tag\Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::factory(10)->create()->each(function ($tag) {
            TagProfile::factory()->for($tag)->create();
        });
    }
}
