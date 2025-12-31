<?php

namespace Modules\CodeSpace\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\CodeSpace\Http\Requests\CreateRequest;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Services\CodeSpaceService;

class CodeSpaceCrudController extends Controller
{
    protected CodeSpaceService $codeSpaceService;

    public function __construct(CodeSpaceService $codeSpaceService)
    {
        $this->codeSpaceService = $codeSpaceService;
    }

    /**
     * Gets CodeSpace data.
     */
    public function get(Request $request): JsonResponse
    {
        $slug      = $request->route('slug');
        $id        = $this->codeSpaceService->getId($slug);
        $data      = $this->codeSpaceService->getData($id);

        return response()->json($data);
    }

    /**
     * Creates a new CodeSpace.
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $slug = Str::uuid();

        CodeSpace::create([
            'name'         => $data['name'],
            'slug'         => $slug,
            'files'        => $data['files'],
            'user_id'      => Auth::id(),
        ]);

        return response()->json(['slug' => $slug]);
    }

    /**
     * Edits a existing CodeSpace.
     */
    public function edit(CreateRequest $request, CodeSpace $code): JsonResponse
    {
        $data = $request->validated();

        $code->update([
            'name'  => $data['name'],
            'files' => $data['files'],
        ]);

        return response()->json(['slug' => $code->slug]);
    }

    /**
     * Lists user's CodeSpaces.
     */
    public function list(): JsonResponse
    {
        $codes = $this->codeSpaceService->listCodes(Auth::id());

        return response()->json($codes);
    }
}
