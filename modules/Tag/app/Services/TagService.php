<?php

namespace Modules\Tag\Services;

use Illuminate\Support\Facades\Cache;
use Modules\News\Models\News;
use Modules\React\Services\ReactService;
use Modules\Tag\Data\TagData;
use Modules\Tag\Data\TagProfileData;
use Modules\Tag\Mappers\TagMapper;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;

class TagService
{
    private ReactService $reactService;

    public function __construct(ReactService $reactService) {
        $this->reactService = $reactService;
    }

    /**
     * Retrieve tag data by its ID.
     *
     * @param int $tagId
     *
     * @return TagData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $tagId): TagData
    {
        $tag = Cache::rememberForever("tag:{$tagId}", function () use ($tagId) {
            return Tag::select(['id', 'name', 'slug'])
                ->findOrFail($tagId);
        });

        return TagMapper::toData($tag);
    }

    /**
     * Retrieve the ID of a tag by its slug.
     *
     * @param string $slug
     *
     * @return int
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("tag:{$slug}:id", function () use ($slug) {
            return Tag::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    /**
     * Retrieve profile information for a given tag.
     *
     * @param int $tagId
     *
     * @return TagProfileData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProfileData(int $tagId): TagProfileData
    {
        return Cache::rememberForever("tag:{$tagId}:profile", function () use ($tagId) {
            $profile = TagProfile::where('tag_id', $tagId)
                ->select(['id', 'description', 'color'])
                ->with('categories:name,slug')
                ->firstOrFail();

            return TagMapper::toProfileData($profile);
        });
    }

    /**
     * Count the number of news posts associated with a tag.
     *
     * @param int $tagId
     *
     * @return int
     */
    public function getArticlesCount(int $tagId): int
    {
        return $this->reactService->getCount(Tag::class, $tagId, "articles");
    }

    /**
     * Get the number of news for a tag by id.
     *
     * @param int $tagId
     *
     * @return int
     */
    public function getNewsCount(int $tagId): int
    {
        return $this->reactService->getCount(Tag::class, $tagId, "news");
    }

    /**
     * Retrieve a list of tags related to a specific category.
     *
     * @param int $tagId
     *
     * @return array<TagData>
     */
    public function listRelated(int $tagId): array
    {
        return Cache::rememberForever("tag:{$tagId}:related", function () use ($tagId) {
            $tagProfile = TagProfile::with('categories')->where('tag_id', $tagId)->first();

            if (! $tagProfile) {
                return [];
            }

            $categoryIds = $tagProfile->categories->pluck('id')->toArray();

            if (count($categoryIds) === 0) {
                return [];
            }

            $relatedTags = Tag::whereHas('profile.categories', function ($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            })
                ->where('id', '!=', $tagId)
                ->distinct()
                ->get();

            return TagMapper::collection($relatedTags);
        });
    }
}
