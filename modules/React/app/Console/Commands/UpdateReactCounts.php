<?php

namespace Modules\React\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Modules\Article\Models\Article;
use Modules\Course\Models\Course;
use Modules\Entry\Models\Entry;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Follow;
use Modules\React\Models\Like;
use Modules\React\Services\ReactService;

class UpdateReactCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'react:update-counts {--model= : The specific model to update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and update counts for reactable models';

    /**
     * Execute the console command.
     */
    public function handle(ReactService $reactService): void
    {
        $models = $this->option('model') ? [$this->option('model')] : [
            Article::class,
            Comment::class,
            User::class,
            Entry::class,
            Course::class,
        ];

        foreach ($models as $modelClass) {
            $this->info("Processing {$modelClass}...");

            $query = $modelClass::query();

            $query->chunk(100, function (Collection $models) use ($reactService, $modelClass) {
                /** @var \Illuminate\Database\Eloquent\Model $model */
                foreach ($models as $model) {
                    $this->updateCountsForModel($model, $reactService, $modelClass);
                }
            });
        }

        $this->info('Counts updated successfully.');
    }

    protected function updateCountsForModel($model, ReactService $reactService, string $modelClass): void
    {
        if ($modelClass === Article::class) {
            $likesCount = Like::where('likeable_type', $modelClass)
                ->where('likeable_id', $model->id)
                ->count();
            $reactService->setCount($modelClass, $model->id, 'likes', $likesCount);

            $dislikesCount = Dislike::where('dislikeable_type', $modelClass)
                ->where('dislikeable_id', $model->id)
                ->count();
            $reactService->setCount($modelClass, $model->id, 'dislikes', $dislikesCount);

            $commentsCount = Comment::where('commentable_type', $modelClass)
                ->where('commentable_id', $model->id)
                ->count();
            $reactService->setCount($modelClass, $model->id, 'comments', $commentsCount);
        }

        if ($modelClass === Comment::class) {
            $likesCount = Like::where('likeable_type', $modelClass)
                ->where('likeable_id', $model->id)
                ->count();
            $reactService->setCount($modelClass, $model->id, 'likes', $likesCount);

            $dislikesCount = Dislike::where('dislikeable_type', $modelClass)
                ->where('dislikeable_id', $model->id)
                ->count();
            $reactService->setCount($modelClass, $model->id, 'dislikes', $dislikesCount);

            $repliesCount = Comment::where('parent_id', $model->id)->count();
            $reactService->setCount($modelClass, $model->id, 'replies', $repliesCount);
        }

        if ($modelClass === User::class) {
            $followersCount = Follow::where('followable_type', $modelClass)
                ->where('followable_id', $model->id)
                ->count();
            $reactService->setCount($modelClass, $model->id, 'followers', $followersCount);

            $followingsCount = Follow::where('follower_id', $model->id)->count();
            $reactService->setCount($modelClass, $model->id, 'followings', $followingsCount);
        }
    }
}
