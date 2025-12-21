<?php

namespace Modules\Article\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Article\Services\ArticleService;
use Modules\Recommend\Services\FeedService;

class ArticleController extends Controller
{
    protected ArticleService $articleService;

    protected SeoService $seoService;

    protected FeedService $feedService;

    public function __construct(ArticleService $articleService, SeoService $seoService, FeedService $feedService)
    {
        $this->articleService = $articleService;
        $this->seoService     = $seoService;
        $this->feedService    = $feedService;
    }

    /**
     * Display a article detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view(Request $request): Response
    {
        $slug      = $request->route('slug');
        $articleId = $this->articleService->getId($slug);
        $article   = $this->articleService->getData($articleId);

        return Inertia::render('Article/Detail', [
            'article' => $article,
        ])->withViewData(['seo' => $this->seoService->getArticleSEO($article, $article->user)]);
    }
}
