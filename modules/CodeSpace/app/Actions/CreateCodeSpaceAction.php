<?php

namespace Modules\CodeSpace\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CodeSpace\Models\CodeSpace;

class CreateCodeSpaceAction
{
    /**
     * @param array{
     *  name: string,
     *  files: array<string, array{name: string, language: string, content: string}>,
     * } $data
     */
    public function execute(array $data): CodeSpace
    {
        return DB::transaction(function () use ($data) {
            $slug = Str::uuid();

            return CodeSpace::create([
                'name'         => $data['name'],
                'slug'         => $slug,
                'files'        => $data['files'],
                'user_id'      => Auth::id(),
            ]);
        });
    }
}
