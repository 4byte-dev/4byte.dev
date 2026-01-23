<?php

namespace Modules\CodeSpace\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CodeSpace\Actions\CreateCodeSpaceAction;
use Modules\CodeSpace\Actions\EditCodeSpaceAction;
use Modules\CodeSpace\Http\Requests\CreateRequest;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Services\CodeSpaceService;

class CodeSpaceCrudController extends Controller
{
    public function __construct(
        protected CodeSpaceService $codeSpaceService,
        protected CreateCodeSpaceAction $createCodeSpaceAction,
        protected EditCodeSpaceAction $editCodeSpaceAction,
    ) {
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

        $codeSpace = $this->createCodeSpaceAction->execute($data);

        return response()->json(['slug' => $codeSpace->slug]);
    }

    /**
     * Edits a existing CodeSpace.
     */
    public function edit(CreateRequest $request, CodeSpace $code): JsonResponse
    {
        $data = $request->validated();

        $this->editCodeSpaceAction->execute($code, $data);

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
