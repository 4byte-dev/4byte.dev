<?php

namespace Modules\Recommend\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;
use Modules\Entry\Models\Entry;
use Modules\React\Models\Comment;
use Modules\React\Models\Dislike;
use Modules\React\Models\Follow;
use Modules\React\Models\Like;
use Modules\React\Models\Save;
use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Classes\GorseUser;
use Modules\Recommend\Services\GorseService;

class UploadRecommendations extends Command
{
    protected $signature = 'recommendation:upload';

    protected $description = 'Upload items and reactions to recommendation engine';

    protected GorseService $gorseService;

    public function handle(): void
    {
        $this->gorseService = app(GorseService::class);

        $this->uploadUsers();
        $this->uploadArticles();
        $this->uploadEntries();
        $this->uploadReactions();
        $this->uploadFollows();

        $this->info('✅ All recommendations uploaded successfully!');
    }

    protected function uploadUsers(): void
    {
        User::all()->each(function ($user) {
            $this->gorseService->insertUser(
                new GorseUser((string) $user->id, ['article', 'entry', 'news'], $user->username)
            );
        });
        $this->info('✅ Users uploaded successfully!');
    }

    protected function uploadArticles(): void
    {
        Article::all()->each(function ($article) {
            $tags       = $article->tags->pluck('id')->map(fn ($id) => 'tag:' . $id);
            $categories = $article->categories->pluck('id')->map(fn ($id) => 'category:' . $id);

            $this->gorseService->insertItem(
                new GorseItem(
                    'article:' . $article->id,
                    ['article', "user:{$article->user_id}"],
                    $tags->merge($categories)->merge(['article', "user:{$article->user_id}"])->all(),
                    $article->slug,
                    $article->status !== ArticleStatus::PUBLISHED,
                    Carbon::parse($article->published_at)->toDateTimeString()
                )
            );
        });
        $this->info('✅ Articles uploaded successfully!');
    }

    protected function uploadEntries(): void
    {
        Entry::all()->each(function ($entry) {
            $this->gorseService->insertItem(
                new GorseItem(
                    'entry:' . $entry->id,
                    ['entry', "user:{$entry->user_id}"],
                    [],
                    '',
                    false,
                    Carbon::now()->toDateTimeString()
                )
            );
        });
        $this->info('✅ Entries uploaded successfully!');
    }

    protected function uploadReactions(): void
    {
        $reactionTypes = [
            'like'    => Like::class,
            'dislike' => Dislike::class,
            'comment' => Comment::class,
            'save'    => Save::class,
        ];

        $morphTypes = [
            'like'    => 'likeable',
            'dislike' => 'dislikeable',
            'comment' => 'commentable',
            'save'    => 'saveable',
        ];

        foreach ($reactionTypes as $type => $model) {
            $model::all()->each(function ($item) use ($type, $morphTypes) {
                /** @var Like|Dislike|Comment|Save $item */
                /** @var \Illuminate\Database\Eloquent\Model $morph */
                $morph = $item->{$morphTypes[$type]};
                $this->gorseService->insertFeedback(
                    new GorseFeedback(
                        $type,
                        (string) $item->user_id,
                        strtolower(class_basename($morph)) . ':' . $morph->id, /* @phpstan-ignore-line */
                        '',
                        Carbon::now()
                    )
                );
            });
            $this->info('✅ ' . ucfirst($type) . 's uploaded successfully!');
        }
    }

    protected function uploadFollows(): void
    {
        Follow::all()->each(function ($follow) {
            $gorseUser = $this->gorseService->getUser((string) $follow->follower_id);
            $gorseUser->addLabel(strtolower(class_basename($follow->followable)) . ':' . $follow->followable->id); /* @phpstan-ignore-line */
            $this->gorseService->updateUser($gorseUser);
        });
        $this->info('✅ Follows uploaded successfully!');
    }
}
