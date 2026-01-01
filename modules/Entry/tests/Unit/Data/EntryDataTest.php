<?php

namespace Modules\Entry\Tests\Unit\Data;

use App\Models\User;
use Carbon\Carbon;
use Mockery;
use Mockery\MockInterface;
use Modules\Entry\Data\EntryData;
use Modules\Entry\Models\Entry;
use Modules\Entry\Tests\TestCase;
use Modules\User\Data\UserData;

class EntryDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $userData = new UserData(
            id: 5,
            name: 'Entry User',
            username: 'entryuser',
            avatar: '',
            followers: 12,
            followings: 4,
            isFollowing: true,
            created_at: now()
        );

        $entryData = new EntryData(
            id: 1,
            slug: 'test-entry',
            content: 'Entry content',
            media: [
                [
                    'image'      => 'https://cdn.4byte.dev/entry.png',
                    'responsive' => [],
                    'srcset'     => '',
                ],
            ],
            user: $userData,
            likes: 10,
            dislikes: 2,
            comments: 5,
            isLiked: true,
            isDisliked: false,
            isSaved: true,
            canUpdate: false,
            canDelete: false,
            published_at: now()
        );

        $this->assertSame(1, $entryData->id);
        $this->assertSame('test-entry', $entryData->slug);
        $this->assertSame('Entry content', $entryData->content);

        $this->assertIsArray($entryData->media);
        $this->assertSame(
            'https://cdn.4byte.dev/entry.png',
            $entryData->media[0]['image']
        );

        $this->assertSame(10, $entryData->likes);
        $this->assertSame(2, $entryData->dislikes);
        $this->assertSame(5, $entryData->comments);

        $this->assertTrue($entryData->isLiked);
        $this->assertFalse($entryData->isDisliked);
        $this->assertTrue($entryData->isSaved);

        $this->assertFalse($entryData->canUpdate);
        $this->assertFalse($entryData->canDelete);

        $this->assertInstanceOf(Carbon::class, $entryData->published_at);
        $this->assertSame('entry', $entryData->type);

        $this->assertInstanceOf(UserData::class, $entryData->user);
        $this->assertSame(5, $entryData->user->id);
        $this->assertSame('Entry User', $entryData->user->name);
        $this->assertSame('entryuser', $entryData->user->username);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $entry = Entry::factory()->create([
            'content' => 'Entry Content',
        ]);

        $user = User::factory()->create([
            'name'     => 'User Name',
            'username' => 'username',
        ]);

        $userData = UserData::fromModel($user);

        $entryData = EntryData::fromModel($entry, $userData);

        $this->assertSame(0, $entryData->id);
        $this->assertSame($entry->slug, $entryData->slug);
        $this->assertSame('Entry Content', $entryData->content);

        $this->assertSame(0, $entryData->likes);
        $this->assertSame(0, $entryData->dislikes);
        $this->assertSame(0, $entryData->comments);

        $this->assertFalse($entryData->isLiked);
        $this->assertFalse($entryData->isDisliked);
        $this->assertFalse($entryData->isSaved);

        $this->assertFalse($entryData->canUpdate);
        $this->assertFalse($entryData->canDelete);

        $this->assertInstanceOf(Carbon::class, $entryData->published_at);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $entry = Entry::factory()->create();
        $user  = User::factory()->create();

        $userData = UserData::fromModel($user);

        $entryData = EntryData::fromModel($entry, $userData, true);

        $this->assertSame($entry->id, $entryData->id);
    }

    public function test_it_sets_published_at_null_when_flag_is_false(): void
    {
        $entry = Entry::factory()->create();
        $user  = User::factory()->create();

        $userData = UserData::fromModel($user);

        $entryData = EntryData::fromModel(
            entry: $entry,
            user: $userData,
            setId: false,
            setPublished: false
        );

        $this->assertNull($entryData->published_at);
    }

    public function test_it_uses_model_methods_for_like_and_dislike_state(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $userData = UserData::fromModel($user);

        /** @var Entry|MockInterface $entry */
        $entry             = Mockery::mock(Entry::class)->makePartial();
        $entry->id         = 10;
        $entry->slug       = 'mock-entry';
        $entry->content    = 'Mock content';
        $entry->created_at = now();

        $entry->shouldReceive('getContentImages')
            ->once()
            ->andReturn([]);

        $entry->shouldReceive('likesCount')
            ->once()
            ->andReturn(20);

        $entry->shouldReceive('dislikesCount')
            ->once()
            ->andReturn(4);

        $entry->shouldReceive('commentsCount')
            ->once()
            ->andReturn(8);

        $entry->shouldReceive('isLikedBy')
            ->once()
            ->with($user->id)
            ->andReturn(true);

        $entry->shouldReceive('isDislikedBy')
            ->once()
            ->with($user->id)
            ->andReturn(false);

        $entry->shouldReceive('isSavedBy')
            ->once()
            ->with($user->id)
            ->andReturn(true);

        $data = EntryData::fromModel($entry, $userData, true);

        $this->assertSame(10, $data->id);
        $this->assertSame(20, $data->likes);
        $this->assertSame(4, $data->dislikes);
        $this->assertSame(8, $data->comments);

        $this->assertTrue($data->isLiked);
        $this->assertFalse($data->isDisliked);
        $this->assertTrue($data->isSaved);
    }

    public function test_it_sets_like_states_as_false_for_guest_user(): void
    {
        $user     = User::factory()->create();
        $userData = UserData::fromModel($user);

        /** @var Entry|MockInterface $entry */
        $entry             = Mockery::mock(Entry::class)->makePartial();
        $entry->id         = 1;
        $entry->slug       = 'guest-entry';
        $entry->content    = 'Guest content';
        $entry->created_at = now();

        $entry->shouldReceive('getContentImages')
            ->once()
            ->andReturn([]);

        $entry->shouldReceive('likesCount')
            ->once()
            ->andReturn(0);

        $entry->shouldReceive('dislikesCount')
            ->once()
            ->andReturn(0);

        $entry->shouldReceive('commentsCount')
            ->once()
            ->andReturn(0);

        $entry->shouldReceive('isLikedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $entry->shouldReceive('isDislikedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $entry->shouldReceive('isSavedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $data = EntryData::fromModel($entry, $userData);

        $this->assertFalse($data->isLiked);
        $this->assertFalse($data->isDisliked);
        $this->assertFalse($data->isSaved);
    }
}
