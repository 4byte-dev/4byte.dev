<?php

namespace Modules\Entry\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Entry\Models\Entry;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

class EntrySeeder extends Seeder
{
    public function run(): void
    {
        Entry::factory(20)->create()->each(function (Entry $entry) {
            Like::factory(3)->forModel($entry)->create();
            Dislike::factory(3)->forModel($entry)->create();
            Save::factory(3)->forModel($entry)->create();
            Comment::factory(5)->forModel($entry)->create();
        });
    }
}
