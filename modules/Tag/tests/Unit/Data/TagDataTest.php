<?php

namespace Modules\Tag\Tests\Unit\Data;

use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use Modules\Tag\Data\TagData;
use Modules\Tag\Mappers\TagMapper;
use Modules\Tag\Models\Tag;
use Modules\Tag\Tests\TestCase;

class TagDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $data = new TagData(
            id: 1,
            name: 'Test Tag',
            slug: 'test-tag',
            followers: 10,
            isFollowing: true
        );

        $this->assertSame(1, $data->id);
        $this->assertSame('Test Tag', $data->name);
        $this->assertSame('test-tag', $data->slug);
        $this->assertSame(10, $data->followers);
        $this->assertTrue($data->isFollowing);

        $this->assertSame('tag', $data->type);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Inertia',
            'slug' => 'inertia',
        ]);

        $tagData = TagMapper::toData($tag);

        $this->assertSame(0, $tagData->id);
        $this->assertSame('Inertia', $tagData->name);
        $this->assertSame('inertia', $tagData->slug);
        $this->assertSame('tag', $tagData->type);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $tagData = TagMapper::toData($tag, true);

        $this->assertSame($tag->id, $tagData->id);
        $this->assertSame('Laravel', $tagData->name);
        $this->assertSame('laravel', $tagData->slug);
        $this->assertSame('tag', $tagData->type);
    }

    public function test_it_uses_model_methods_for_followers_and_follow_state(): void
    {
        $userId = 123;
        Auth::shouldReceive('id')->once()->andReturn($userId);

        /** @var Tag|MockInterface $tag */
        $tag       = Mockery::mock(Tag::class)->makePartial();
        $tag->id   = 99;
        $tag->name = 'PHP';
        $tag->slug = 'php';

        $tag->shouldReceive('followersCount')
            ->once()
            ->andReturn(42);

        $tag->shouldReceive('isFollowedBy')
            ->once()
            ->with($userId)
            ->andReturn(true);

        $data = TagMapper::toData($tag, true);

        $this->assertSame(99, $data->id);
        $this->assertSame(42, $data->followers);
        $this->assertTrue($data->isFollowing);
    }

    public function test_it_handles_guest_user(): void
    {
        Auth::shouldReceive('id')->once()->andReturn(null);

        /** @var Tag|MockInterface $tag */
        $tag       = Mockery::mock(Tag::class)->makePartial();
        $tag->id   = 1;
        $tag->name = 'Guest';
        $tag->slug = 'guest';

        $tag->shouldReceive('followersCount')->once()->andReturn(0);
        $tag->shouldReceive('isFollowedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $data = TagMapper::toData($tag);

        $this->assertFalse($data->isFollowing);
    }
}
