<?php

namespace Modules\Entry\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Entry\Actions\CreateEntryAction;
use Modules\Entry\Http\Requests\CreateRequest;
use Modules\Entry\Services\EntryService;

class EntryCrudController extends Controller
{
    public function __construct(
        protected EntryService $entryService,
        protected CreateEntryAction $createEntryAction,
    ) {
    }

    /**
     * Creates a new Entry.
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $entry = $this->createEntryAction->execute($data);

        return response()->json(['slug' => $entry->slug]);
    }
}
