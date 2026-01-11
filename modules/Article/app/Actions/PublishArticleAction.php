<?php

namespace Modules\Article\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Models\Article;

class PublishArticleAction
{
    public function execute(Article $article): Article
    {
        return DB::transaction(function () use ($article) {
            $article->update([
                'status'       => ArticleStatus::PUBLISHED,
                'published_at' => now(),
            ]);

            event(new ArticlePublishedEvent($article));

            return $article;
        });
    }
}
