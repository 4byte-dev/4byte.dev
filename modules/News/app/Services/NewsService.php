<?php

namespace Modules\News\Services;

use Illuminate\Support\Facades\Cache;
use Modules\News\Data\NewsData;
use Modules\News\Models\News;
use Modules\User\Services\UserService;

class NewsService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Retrieve news data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $newsId): NewsData
    {
        $news = Cache::rememberForever("news:{$newsId}", function () use ($newsId) {
            return News::where('status', 'PUBLISHED')
                ->with(['categories:id,name,slug', 'tags:id,name,slug'])
                ->select(['id', 'title', 'slug', 'content', 'excerpt', 'published_at', 'user_id'])
                ->findOrFail($newsId);
        });

        $user = $this->userService->getData($news->user_id);

        return NewsData::fromModel($news, $user);
    }

    /**
     * Retrieve the ID of a news by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("news:{$slug}:id", function () use ($slug) {
            return News::where('status', 'PUBLISHED')
                ->where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }
}
