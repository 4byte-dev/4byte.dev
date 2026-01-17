<?php

namespace Modules\Article\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Models\Article;
use Modules\Article\Support\SlugGenerator;
use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;
use Stevebauman\Purify\Facades\Purify;

class CreateArticleAction
{
    /**
     * @param array{
     *     title: string,
     *     published?: bool,
     *     excerpt?: string,
     *     content?: string,
     *     categories?: array<string>,
     *     tags?: array<string>,
     *     image?: UploadedFile,
     *     sources?: array<int, array{
     *         url: string,
     *         date: string
     *     }>
     * } $data
     * @param UploadedFile|null $image
     * @param int|null $userId
     * @param array<string, ?UploadedFile> $contentImages
     */
    public function execute(array $data, ?UploadedFile $image = null, ?int $userId = null, array $contentImages = []): Article
    {
        return DB::transaction(function () use ($data, $image, $userId, $contentImages) {
            $userId ??= Auth::id();
            $title    = $data['title'];
            $isDraft  = ! ($data['published'] ?? false);

            $slugGenerator = new SlugGenerator();
            $slug          = $slugGenerator->generate($title);

            $content = isset($data['content']) ? Purify::clean($data['content']) : null;

            $article = Article::create([
                'title'        => $title,
                'slug'         => $slug,
                'excerpt'      => $data['excerpt'] ?? null,
                'content'      => $content,
                'status'       => $isDraft ? ArticleStatus::DRAFT : ArticleStatus::PUBLISHED,
                'published_at' => $isDraft ? null : now(),
                'sources'      => $data['sources'] ?? [],
                'user_id'      => $userId,
            ]);

            if ($image) {
                $article->addMedia($image)->toMediaCollection('cover');
            }

            if (! empty($contentImages) && $content) {
                foreach ($contentImages as $placeholder => $file) {
                    if ($file instanceof UploadedFile) {
                        $media   = $article->addMedia($file)->toMediaCollection('content');
                        $url     = $media->getUrl();
                        $content = str_replace($placeholder, $url, $content);
                    }
                }
                $article->update(['content' => $content]);
            }

            if (! empty($data['categories'])) {
                $categoryIds = Category::whereIn('slug', $data['categories'])->pluck('id')->toArray();
                $article->categories()->sync($categoryIds);
            }

            if (! empty($data['tags'])) {
                $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
                $article->tags()->sync($tagIds);
            }

            if (! $isDraft) {
                event(new ArticlePublishedEvent($article));
            }

            return $article;
        });
    }
}
