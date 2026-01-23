<?php

namespace Modules\Article\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Models\Article;
use Modules\Article\Support\SlugGenerator;
use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;
use Stevebauman\Purify\Facades\Purify;

class UpdateArticleAction
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
    public function execute(Article $article, array $data, ?UploadedFile $image = null, ?int $userId = null, array $contentImages = []): Article
    {
        return DB::transaction(function () use ($article, $data, $image, $userId, $contentImages) {
            $userId ??= Auth::id();
            $title    = $data['title'];
            $isDraft  = ! ($data['published'] ?? false);

            $slugGenerator = new SlugGenerator();
            $slug          = $slugGenerator->generate($title, $article->id);

            $content = isset($data['content']) ? Purify::clean($data['content']) : null;

            if (\count($contentImages) > 0 && $content) {
                foreach ($contentImages as $placeholder => $file) {
                    if ($file instanceof UploadedFile) {
                        $media   = $article->addMedia($file)->toMediaCollection('content');
                        $url     = $media->getUrl();
                        $content = str_replace($placeholder, $url, $content);
                    }
                }
            }

            $updateData = [
                'title'        => $title,
                'slug'         => $slug,
                'excerpt'      => $data['excerpt'] ?? null,
                'content'      => $content,
                'status'       => $isDraft ? ArticleStatus::DRAFT : ArticleStatus::PUBLISHED,
                'published_at' => $isDraft ? null : now(),
                'sources'      => $data['sources'] ?? [],
            ];

            if ($userId) {
                $updateData['user_id'] = $userId;
            }

            $article->update($updateData);

            if ($image) {
                $article->addMedia($image)->toMediaCollection('cover');
            }

            if (\array_key_exists('categories', $data)) {
                $categoryIds = Category::whereIn('slug', $data['categories'])->pluck('id')->toArray();
                $article->categories()->sync($categoryIds);
            }

            if (\array_key_exists('tags', $data)) {
                $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
                $article->tags()->sync($tagIds);
            }

            if ($article->isDirty('slug')) {
                Cache::forget("article:{$article->getOriginal('slug')}:id");
            }

            Cache::forget("article:{$article->id}");

            return $article;
        });
    }
}
