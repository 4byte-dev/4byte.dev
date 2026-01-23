<?php

namespace Modules\CodeSpace\Data;

use Illuminate\Support\Carbon;
use Modules\User\Data\UserData;

readonly class CodeSpaceData
{
    /**
     * @param array<string, array{name: string, language: string, content: string}> $files
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?array $files,
        public readonly ?UserData $user,
        public readonly ?Carbon $updated_at,
        public readonly string $type = 'codespace'
    ) {
    }
}
