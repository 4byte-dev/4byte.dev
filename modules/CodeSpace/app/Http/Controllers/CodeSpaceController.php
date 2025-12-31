<?php

namespace Modules\CodeSpace\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\CodeSpace\Services\CodeSpaceService;
use Modules\User\Services\UserService;

class CodeSpaceController extends Controller
{
    protected SeoService $seoService;

    protected CodeSpaceService $codeSpaceService;

    protected UserService $userService;

    public function __construct(SeoService $seoService, CodeSpaceService $codeSpaceService, UserService $userService)
    {
        $this->seoService       = $seoService;
        $this->codeSpaceService = $codeSpaceService;
        $this->userService      = $userService;
    }

    /**
     * Display a codespace detail page.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function view(Request $request): Response
    {
        $slug           = $request->route('slug');
        $id             = null;
        $codeSpace      = null;

        if ($slug !== null && $slug !== '') {
            $id            = $this->codeSpaceService->getId($slug);
            $codeSpace     = $this->codeSpaceService->getData($id);
        }

        return Inertia::render('CodeSpace/Detail', [
            'slug'      => $slug,
            'codeSpace' => $codeSpace,
        ])->withViewData(['seo' => $this->seoService->getCodeSpaceSeo($codeSpace, $codeSpace?->user)]);
    }
}
