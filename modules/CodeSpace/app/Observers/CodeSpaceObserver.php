<?php

namespace Modules\CodeSpace\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\CodeSpace\Models\CodeSpace;

class CodeSpaceObserver
{
    /**
     * Handle the "updated" event for the CodeSpace model.
     */
    public function updated(CodeSpace $codeSpace): void
    {
        Cache::forget("codespace:{$codeSpace->id}");
    }

    /**
     * Handle the "deleted" event for the CodeSpace model.
     */
    public function deleted(CodeSpace $codeSpace): void
    {
        Cache::forget("codespace:{$codeSpace->slug}:id");
        Cache::forget("codespace:{$codeSpace->id}");
        Cache::forget("codespace:{$codeSpace->user_id}:codespaces");
    }
}
