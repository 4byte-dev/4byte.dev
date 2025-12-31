<?php

namespace Modules\CodeSpace\Services;

use Illuminate\Support\Facades\Cache;
use Modules\CodeSpace\Data\CodeSpaceData;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\User\Services\UserService;

class CodeSpaceService
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Retrieve CodeSpace data by its ID.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getData(int $codeSpaceId): CodeSpaceData
    {
        $codeSpace = Cache::rememberForever("codespace:{$codeSpaceId}", function () use ($codeSpaceId) {
            return CodeSpace::query()
                ->select(['id', 'user_id', 'name', 'slug', 'files'])
                ->findOrFail($codeSpaceId);
        });

        $user = $this->userService->getData($codeSpace->user_id);

        return CodeSpaceData::fromModel($codeSpace, $user);
    }

    /**
     * Retrieve the ID of a CodeSpace by its slug.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getId(string $slug): int
    {
        return Cache::rememberForever("codespace:{$slug}:id", function () use ($slug) {
            return CodeSpace::where('slug', $slug)
                ->select(['id'])
                ->firstOrFail()->id;
        });
    }

    /**
     * Lists user's CodeSpaces.
     *
     * @return array<int, CodeSpaceData>
     */
    public function listCodes(int $userId): array
    {
        $codeSpaces = Cache::rememberForever("codespace:{$userId}:codespaces", function () use ($userId) {
            return CodeSpace::where('user_id', $userId)
                ->select('id', 'name', 'slug', 'updated_at')
                ->get()
                ->toArray();
        });

        return CodeSpaceData::collect($codeSpaces);
    }
}
