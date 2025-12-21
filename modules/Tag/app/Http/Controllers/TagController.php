<?php

namespace Modules\Tag\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Tag\Services\TagService;

class TagController extends Controller
{
    protected TagService $tagService;

    protected SeoService $seoService;

    public function __construct(TagService $tagService, SeoService $seoService)
    {
        $this->tagService      = $tagService;
        $this->seoService      = $seoService;
    }

    /**
     * Display a tag detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view(Request $request): Response
    {
        $slug       = $request->route('slug');
        $tagId      = $this->tagService->getId($slug);
        $tag        = $this->tagService->getData($tagId);
        $profile    = $this->tagService->getProfileData($tagId);
        $articles   = $this->tagService->getArticlesCount($tagId);
        $news       = $this->tagService->getNewsCount($tagId);
        $tags       = $this->tagService->listRelated($tagId);

        return Inertia::render('Tag/Detail', [
            'tag'      => $tag,
            'profile'  => $profile,
            'articles' => $articles,
            'news'     => $news,
            'tags'     => $tags,
        ])->withViewData(['seo' => $this->seoService->getTagSeo($tag, $profile)]);
    }
}
