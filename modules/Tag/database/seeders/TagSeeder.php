<?php

namespace Modules\Tag\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        Tag::factory(10)->create()->each(function ($tag) {
            TagProfile::factory()->for($tag)->create();
        });
    }
}
