<?php

namespace Packages\Tag\Tests\Unit\Database\Factories;

use Illuminate\Support\Str;
use Packages\Tag\Models\Tag;
use Packages\Tag\Tests\TestCase;

class TagFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_tag(): void
    {
        $tag = Tag::factory()->create();

        $this->assertInstanceOf(Tag::class, $tag);

        $this->assertNotEmpty($tag->name);
        $this->assertNotEmpty($tag->slug);
    }

    public function test_name_starts_with_uppercase_letter(): void
    {
        $tag = Tag::factory()->create();

        $this->assertSame(
            ucfirst(strtolower($tag->name)),
            $tag->name
        );
    }

    public function test_slug_is_a_valid_slug(): void
    {
        $tag = Tag::factory()->create();

        $this->assertSame(
            Str::slug($tag->name),
            $tag->slug
        );
    }

    public function test_slug_contains_only_slug_characters(): void
    {
        $tag = Tag::factory()->create();

        $this->assertMatchesRegularExpression(
            '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            $tag->slug
        );
    }

    public function test_factory_creates_unique_tags(): void
    {
        $tags = Tag::factory()->count(10)->create();

        $this->assertCount(
            10,
            $tags->pluck('name')->unique()
        );

        $this->assertCount(
            10,
            $tags->pluck('slug')->unique()
        );
    }
}
