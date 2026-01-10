<?php

namespace Modules\Article\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Article\Models\Article;

class PublishArticleAction
{
    public function execute(Article $article): Article
    {
        return DB::transaction(function () use ($article) {
            $article->update([
                'status'       => 'PUBLISHED',
                'published_at' => now(),
            ]);

            return $article;
        });
    }
}
