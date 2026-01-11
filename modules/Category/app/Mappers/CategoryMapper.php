<?php

namespace Modules\Category\Mappers;

use Illuminate\Support\Facades\Auth;
use Modules\Category\Data\CategoryData;
use Modules\Category\Data\CategoryProfileData;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProfile;

class CategoryMapper
{
    /**
     * Create a CategoryData instance from a Category model.
     */
    public static function toData(Category $category, bool $setId = false): CategoryData
    {
        return new CategoryData(
            id: $setId ? $category->id : 0,
            name: $category->name,
            slug: $category->slug,
            followers: $category->followersCount(),
            isFollowing: $category->isFollowedBy(Auth::id())
        );
    }

    /**
     * Create a CategoryProfileData instance from a CategoryProfile model.
     */
    public static function toProfileData(CategoryProfile $profile, bool $setId = false): CategoryProfileData
    {
        return new CategoryProfileData(
            id: $setId ? $profile->id : 0,
            description: $profile->description,
            color: $profile->color
        );
    }

    /**
     * @param iterable<Category> $categories
     * @param bool $setId
     *
     * @return array<CategoryData>
     */
    public static function collection(iterable $categories, bool $setId = false): array
    {
        $data = [];
        foreach ($categories as $category) {
            $data[] = self::toData($category, $setId);
        }

        return $data;
    }
}
