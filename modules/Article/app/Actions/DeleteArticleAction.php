<?php

namespace Modules\Article\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Models\Article;

class DeleteArticleAction
{
    public function execute(Article $article): Article
    {
        return DB::transaction(function () use ($article) {
            $article->delete();

            Cache::forget("article:{$article->slug}:id");
            Cache::forget("article:{$article->id}");

            event(new ArticleDeletedEvent($article));

            return $article;
        });
    }
}
