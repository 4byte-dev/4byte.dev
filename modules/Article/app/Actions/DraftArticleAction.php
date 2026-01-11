<?php

namespace Modules\Article\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;

class DraftArticleAction
{
    public function execute(Article $article): Article
    {
        return DB::transaction(function () use ($article) {
            $article->update([
                'status'       => ArticleStatus::DRAFT,
                'published_at' => null,
            ]);

            return $article;
        });
    }
}
