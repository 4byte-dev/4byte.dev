<?php

namespace Modules\Entry\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Entry\Http\Requests\CreateRequest;
use Modules\Entry\Models\Entry;
use Modules\Entry\Services\EntryService;

class EntryCrudController extends Controller
{
    protected EntryService $entryService;

    public function __construct(EntryService $entryService)
    {
        $this->entryService = $entryService;
    }

    /**
     * Creates a new Entry.
     */
    public function create(CreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $entry = Entry::create([
            'slug'    => Str::uuid(),
            'content' => $data['content'],
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('media')) {
            $entry->addMultipleMediaFromRequest(['media'])
                ->each(function ($fileAdder) {
                    $fileAdder->toMediaCollection('content');
                });
        }

        return response()->json(['slug' => $entry->slug]);
    }
}
