<?php

namespace Modules\Tag\Mappers;

use Illuminate\Support\Facades\Auth;
use Modules\Category\Mappers\CategoryMapper;
use Modules\Tag\Data\TagData;
use Modules\Tag\Data\TagProfileData;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;

class TagMapper
{
    /**
     * Convert Tag entity to TagData.
     */
    public static function toData(Tag $tag, bool $setId = false): TagData
    {
        return new TagData(
            id: $setId ? $tag->id : 0,
            name: $tag->name,
            slug: $tag->slug,
            followers: $tag->followersCount(),
            isFollowing: $tag->isFollowedBy(Auth::id())
        );
    }

    /**
     * Convert TagProfile entity to TagProfileData.
     */
    public static function toProfileData(TagProfile $tagProfile, bool $setId = false): TagProfileData
    {
        return new TagProfileData(
            id: $setId ? $tagProfile->id : 0,
            description: $tagProfile->description,
            color: $tagProfile->color,
            categories: CategoryMapper::collection($tagProfile->categories)
        );
    }

    /**
     * @param iterable<Tag> $tags
     * @param bool $setId
     *
     * @return array<TagData>
     */
    public static function collection(iterable $tags, bool $setId = false): array
    {
        $data = [];
        foreach ($tags as $tag) {
            $data[] = self::toData($tag, $setId);
        }

        return $data;
    }
}
