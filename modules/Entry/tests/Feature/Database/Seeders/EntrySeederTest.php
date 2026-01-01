<?php

namespace Modules\Entry\Tests\Feature\Database\Seeders;

use Modules\Entry\Database\Seeders\EntrySeeder;
use Modules\Entry\Models\Entry;
use Modules\Entry\Tests\TestCase;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Like;
use Modules\React\Models\Save;

class EntrySeederTest extends TestCase
{
    public function test_it_seeds_entries(): void
    {
        $this->seed(EntrySeeder::class);

        $this->assertDatabaseCount(Entry::class, 7);
        $this->assertDatabaseCount(Like::class, 21);
        $this->assertDatabaseCount(Dislike::class, 21);
        $this->assertDatabaseCount(Save::class, 21);
        $this->assertDatabaseCount(Comment::class, 21);
    }
}
