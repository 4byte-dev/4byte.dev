<?php

namespace Modules\Article\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Article\Actions\CreateArticleAction;
use Modules\Article\Actions\UpdateArticleAction;
use Modules\Article\Enums\ArticleStatus;
use Modules\Article\Http\Requests\CreateRequest;
use Modules\Article\Http\Requests\EditRequest;
use Modules\Article\Models\Article;
use Modules\Recommend\Services\FeedService;

class ArticleCrudController extends Controller
{
    public function __construct(
        protected SeoService $seoService,
        protected FeedService $feedService,
        protected CreateArticleAction $createArticleAction,
        protected UpdateArticleAction $updateArticleAction
    ) {
    }

    /**
     * Display a article create page.
     */
    /**
     * Display a article create page.
     */
    public function create(): Response
    {
        $topCategories = $this->feedService->categories();

        $topTags = $this->feedService->tags();

        return Inertia::render('Article/Create', [
            'topCategories' => $topCategories,
            'topTags'       => $topTags,
        ])->withViewData(['seo' => $this->seoService->getArticleCreateSEO()]);
    }

    /**
     * Creates a new Article.
     */
    public function store(CreateRequest $request): JsonResponse
    {
        $isDraft = ! $request->boolean('published', false);

        $data = $request->validated();

        if ($isDraft) {
            $extra = $request->only(['excerpt', 'content', 'categories', 'tags', 'sources']);
            $data  = [...$data, ...$extra];
        }

        $data['published'] = ! $isDraft;

        $article = $this->createArticleAction->execute(
            $data,
            $request->file('image'),
            Auth::id()
        );

        return response()->json(['slug' => $article->slug]);
    }

    /**
     * Display a article edit page.
     */
    public function edit(Article $article): Response
    {
        $topCategories = $this->feedService->categories();

        $topTags = $this->feedService->tags();

        return Inertia::render('Article/Edit', [
            'topCategories' => $topCategories,
            'topTags'       => $topTags,
            'slug'          => $article->slug,
            'article'       => [
                'title'      => $article->title,
                'excerpt'    => $article->excerpt,
                'sources'    => $article->sources,
                'content'    => $article->content,
                'categories' => $article->categories->pluck('slug'),
                'tags'       => $article->tags->pluck('slug'),
                'published'  => $article->status === ArticleStatus::PUBLISHED,
                'image'      => $article->getCoverImage()['image'],
            ],
        ])->withViewData(['seo' => $this->seoService->getArticleEditSEO()]);
    }

    /**
     * Edits a existing Article.
     */
    public function update(EditRequest $request, Article $article): JsonResponse
    {
        $isDraft = ! $request->boolean('published', false);

        $data = $request->validated();

        if ($isDraft) {
            $extra = $request->only(['excerpt', 'content', 'categories', 'tags', 'sources']);
            $data  = [...$data, ...$extra];
        }

        $data['published'] = ! $isDraft;

        $article = $this->updateArticleAction->execute(
            $article,
            $data,
            $request->file('image'),
            Auth::id()
        );

        return response()->json(['slug' => $article->slug]);
    }
}
