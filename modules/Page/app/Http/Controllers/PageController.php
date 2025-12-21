<?php

namespace Modules\Page\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Page\Services\PageService;

class PageController extends Controller
{
    protected PageService $pageService;

    protected SeoService $seoService;

    public function __construct(PageService $pageService, SeoService $seoService)
    {
        $this->pageService = $pageService;
        $this->seoService  = $seoService;
    }

    /**
     * Display a page detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view(Request $request): Response
    {
        $slug   = $request->route('slug');
        $pageId = $this->pageService->getId($slug);
        $page   = $this->pageService->getData($pageId);

        return Inertia::render('Page/Detail', [
            'page' => $page,
        ])->withViewData(['seo' => $this->seoService->getPageSEO($page, $page->user)]);
    }
}
