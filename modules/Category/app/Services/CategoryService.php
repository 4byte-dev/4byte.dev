<?php

namespace Modules\Category\Services;

use Illuminate\Support\Facades\Cache;
use Modules\Category\Data\CategoryData;
use Modules\Category\Data\CategoryProfileData;
use Modules\Category\Mappers\CategoryMapper;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProfile;
use Modules\React\Services\ReactService;
use Modules\Tag\Data\TagData;
use Modules\Tag\Mappers\TagMapper;
use Modules\Tag\Models\Tag;

class CategoryService
{
    private ReactService $reactService;

    public function __construct(ReactService $reactService)
    {
        $this->reactService = $reactService;
    }

    /**
     * Retrieve category data by its ID.
     *
     * @param int $categoryId
     *
     * @return CategoryData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $categoryId): CategoryData
    {
        $category = Cache::rememberForever("category:{$categoryId}", function () use ($categoryId) {
            return Category::select(['id', 'name', 'slug'])
                ->findOrFail($categoryId);
        });

        return CategoryMapper::toData($category);
    }

    /**
     * Retrieve the ID of a category by its slug.
     *
     * @param string $slug
     *
     * @return int
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("category:{$slug}:id", function () use ($slug) {
            return Category::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    /**
     * Retrieve profile information for a given category.
     *
     * @param int $categoryId
     *
     * @return CategoryProfileData
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getProfileData(int $categoryId): CategoryProfileData
    {
        return Cache::rememberForever("category:{$categoryId}:profile", function () use ($categoryId) {
            $profile = CategoryProfile::where('category_id', $categoryId)
                ->select(['description', 'color'])
                ->firstOrFail();

            return CategoryMapper::toProfileData($profile);
        });
    }

    /**
     * Count the number of news posts associated with a category.
     *
     * @param int $categoryId
     *
     * @return int
     */
    public function getArticlesCount(int $categoryId): int
    {
        return $this->reactService->getCount(Category::class, $categoryId, 'articles');
    }

    /**
     * Get the number of news for a category by id.
     *
     * @param int $categoryId
     *
     * @return int
     */
    public function getNewsCount(int $categoryId): int
    {
        return $this->reactService->getCount(Category::class, $categoryId, 'news');
    }

    /**
     * Retrieve a list of tags related to a specific category.
     *
     * @param int $categoryId
     *
     * @return array<TagData>
     */
    public function listTags(int $categoryId): array
    {
        return Cache::rememberForever("category:{$categoryId}:tags", function () use ($categoryId) {
            $tags = Tag::whereHas('profile.categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            })->get();

            return TagMapper::collection($tags);
        });
    }
}
