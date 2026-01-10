<?php

namespace Modules\Article\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Article\Models\Article;
use Modules\Article\Support\SlugGenerator;
use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;

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
     */
    public function execute(array $data, ?UploadedFile $image = null, ?int $userId = null): Article
    {
        return DB::transaction(function () use ($data, $image, $userId) {
            $userId ??= Auth::id();
            $title    = $data['title'];
            $isDraft  = ! ($data['published'] ?? false);

            $slugGenerator = new SlugGenerator();
            $slug          = $slugGenerator->generate($title);

            $article = Article::create([
                'title'        => $title,
                'slug'         => $slug,
                'excerpt'      => $data['excerpt'] ?? null,
                'content'      => $data['content'] ?? null,
                'status'       => $isDraft ? 'DRAFT' : 'PUBLISHED',
                'published_at' => $isDraft ? null : now(),
                'sources'      => $data['sources'] ?? [],
                'user_id'      => $userId,
            ]);

            if ($image) {
                $article->addMedia($image)->toMediaCollection('cover');
            }

            if (! empty($data['categories'])) {
                $categoryIds = Category::whereIn('slug', $data['categories'])->pluck('id')->toArray();
                $article->categories()->sync($categoryIds);
            }

            if (! empty($data['tags'])) {
                $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
                $article->tags()->sync($tagIds);
            }

            return $article;
        });
    }
}
