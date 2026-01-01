<?php

namespace Modules\Entry\Tests\Unit\Database;

use App\Models\User;
use Illuminate\Support\Str;
use Modules\Entry\Models\Entry;
use Modules\Entry\Tests\TestCase;

class EntryFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_entry(): void
    {
        $entry = Entry::factory()->create();

        $this->assertInstanceOf(Entry::class, $entry);
        $this->assertNotNull($entry->content);
        $this->assertIsString($entry->content);
        $this->assertNotEmpty($entry->content);
    }

    public function test_it_creates_entry_linked_to_user(): void
    {
        $user  = User::factory()->create();
        $entry = Entry::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->id, $entry->user_id);
    }

    public function test_slug_is_uuid(): void
    {
        $entry = Entry::factory()->create();

        $this->assertTrue(
            Str::isUuid($entry->slug)
        );
    }

    public function test_slug_is_unique(): void
    {
        $entries = Entry::factory()->count(10)->create();

        $this->assertCount(
            10,
            $entries->pluck('slug')->unique()
        );
    }

    public function test_factory_creates_multiple_entries(): void
    {
        $entries = Entry::factory()->count(5)->create();

        $this->assertCount(5, $entries);

        $entries->each(function ($entry) {
            $this->assertInstanceOf(Entry::class, $entry);
        });
    }
}
