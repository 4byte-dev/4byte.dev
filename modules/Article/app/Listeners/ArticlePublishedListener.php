<?php

namespace Modules\Article\Listeners;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Notifications\ArticlePublishedNotification;

class ArticlePublishedListener implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Handle the event.
     */
    public function handle(ArticlePublishedEvent $event): void
    {
        $article = $event->article;

        $article->user->notify(new ArticlePublishedNotification($article));
        $article->user->notify(
            Notification::make()
                ->title(__('article::messages.published_title'))
                ->success()
                ->body(__('article::messages.published_body', ['title' => $article->title]))
                ->actions([
                    Action::make('view')
                        ->label(__('article::messages.view_article'))
                        ->url(route('article.view', ['slug' => $article->slug]))
                        ->markAsRead()
                        ->openUrlInNewTab()
                        ->button(),
                ])
                ->toDatabase()
        );
    }
}
