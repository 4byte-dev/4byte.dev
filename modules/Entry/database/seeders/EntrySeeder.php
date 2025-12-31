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
        Entry::factory()
            ->count(7)
            ->has(Like::factory()->count(3), 'likes')
            ->has(Dislike::factory()->count(3), 'dislikes')
            ->has(Save::factory()->count(3), 'saves')
            ->has(Comment::factory()->count(3), 'comments')
            ->create();
    }
}
