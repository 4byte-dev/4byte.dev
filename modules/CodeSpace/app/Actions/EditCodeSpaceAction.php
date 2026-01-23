<?php

namespace Modules\CodeSpace\Actions;

use Illuminate\Support\Facades\DB;
use Modules\CodeSpace\Models\CodeSpace;

class EditCodeSpaceAction
{
    /**
     * @param array{
     *  name: string,
     *  files: array<string, array{name: string, language: string, content: string}>,
     * } $data
     */
    public function execute(CodeSpace $codeSpace, array $data): CodeSpace
    {
        return DB::transaction(function () use ($codeSpace, $data) {
            $codeSpace->update([
                'name'         => $data['name'],
                'files'        => $data['files'],
            ]);

            return $codeSpace;
        });
    }
}
