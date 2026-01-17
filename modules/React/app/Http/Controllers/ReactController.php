<?php

namespace Modules\React\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Modules\React\Actions\CommentAction;
use Modules\React\Actions\DislikeAction;
use Modules\React\Actions\FollowAction;
use Modules\React\Actions\LikeAction;
use Modules\React\Actions\SaveAction;
use Modules\React\Actions\UndislikeAction;
use Modules\React\Actions\UnfollowAction;
use Modules\React\Actions\UnlikeAction;
use Modules\React\Actions\UnsaveAction;
use Modules\React\Http\Requests\ReactRequest;
use Modules\React\Services\ReactService;

class ReactController extends Controller
{
    public function __construct(
        protected ReactService $reactService,
        protected LikeAction $likeAction,
        protected UnlikeAction $unlikeAction,
        protected DislikeAction $dislikeAction,
        protected UndislikeAction $undislikeAction,
        protected SaveAction $saveAction,
        protected UnsaveAction $unsaveAction,
        protected FollowAction $followAction,
        protected UnfollowAction $unfollowAction,
        protected CommentAction $commentAction
    ) {
    }

    /**
     * Handle like reaction on a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function like(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId] = $request->resolveTarget();
        $userId               = Auth::id();

        if ($this->reactService->checkLiked($baseClass, $itemId, $userId)) {
            $this->unlikeAction->execute($baseClass, $itemId, $userId);
        } else {
            $this->likeAction->execute($baseClass, $itemId, $userId);
        }

        return response()->noContent(200);
    }

    /**
     * Handle dislike reaction on a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function dislike(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId] = $request->resolveTarget();
        $userId               = Auth::id();

        if ($this->reactService->checkDisliked($baseClass, $itemId, $userId)) {
            $this->undislikeAction->execute($baseClass, $itemId, $userId);
        } else {
            $this->dislikeAction->execute($baseClass, $itemId, $userId);
        }

        return response()->noContent(200);
    }

    /**
     * Handle save action on a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function save(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId] = $request->resolveTarget();
        $userId               = Auth::id();

        if ($this->reactService->checkSaved($baseClass, $itemId, $userId)) {
            $this->unsaveAction->execute($baseClass, $itemId, $userId);
        } else {
            $this->saveAction->execute($baseClass, $itemId, $userId);
        }

        return response()->noContent(200);
    }

    public function comment(ReactRequest $request): Response
    {
        $request->validate([
            'content' => 'required|string|min:20',
            'parent'  => 'nullable|integer',
        ]);

        [$baseClass, $itemId] = $request->resolveTarget();
        $userId               = Auth::id();
        $content              = $request->input('content');
        $parentId             = $request->input('parent', null);

        $this->commentAction->execute($baseClass, $itemId, $content, $userId, $parentId);

        return response()->noContent(200);
    }

    /**
     * Get paginated comments for a model.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function comments(ReactRequest $request): JsonResponse
    {
        $request->validate([
            'page' => 'sometimes|integer|min:1',
        ]);

        [$baseClass, $itemId] = $request->resolveTarget();
        $page                 = $request->input('page', 1);

        $comments = $this->reactService->getComments($baseClass, $itemId, $page, 10);

        return response()->json($comments);
    }

    /**
     * Get replies for a specific comment.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function replies(ReactRequest $request): JsonResponse
    {
        $request->merge([
            'parent' => $request->route('parent'),
        ]);

        $request->validate([
            'page'   => 'sometimes|integer|min:1',
            'parent' => 'required|integer|exists:comments,id',
        ]);

        $parentId             = (int) $request->route('parent');
        [$baseClass, $itemId] = $request->resolveTarget();
        $page                 = $request->input('page', 1);

        if (! $parentId) {
            response()->noContent();
        }

        $commentReplies = $this->reactService->getCommentReplies($baseClass, $itemId, $parentId, $page, 10);

        return response()->json($commentReplies);
    }

    /**
     * Follow or unfollow a model.
     *
     * @return Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function follow(ReactRequest $request): JsonResponse|Response
    {
        [$baseClass, $itemId, $type] = $request->resolveTarget();
        $userId                      = Auth::id();

        $cacheKey = "{$type}:{$itemId}:follow";

        $executed = RateLimiter::attempt(
            key: "{$cacheKey}:{$userId}",
            maxAttempts: 1,
            decaySeconds: 60 * 60 * 24,
            callback: function () use ($baseClass, $itemId, $userId) {
                if ($this->reactService->checkFollowed($baseClass, $itemId, $userId)) {
                    $this->unfollowAction->execute($baseClass, $itemId, $userId);
                } else {
                    $this->followAction->execute($baseClass, $itemId, $userId);
                }
            }
        );

        if (! $executed) {
            return response()->noContent(429);
        }

        return response()->noContent(200);
    }
}
