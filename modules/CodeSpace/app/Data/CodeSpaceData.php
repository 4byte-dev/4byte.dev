<?php

namespace Modules\CodeSpace\Data;

use Illuminate\Support\Carbon;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\User\Data\UserData;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;

class CodeSpaceData extends Data
{
    /**
     * @param array<string, array{name: string, language: string, content: string}> $files
     */
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
        public ?array $files,
        public ?UserData $user,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.uP')]
        public ?Carbon $updated_at,
        public string $type = 'codespace'
    ) {
    }

    /**
     * Create a CodeSpaceData instance from a CodeSpace model.
     */
    public static function fromModel(CodeSpace $codeSpace, UserData $user, bool $setId = false, bool $setUpdatedAt = false): self
    {
        return new self(
            id: $setId ? $codeSpace->id : 0,
            name: $codeSpace->name,
            slug: $codeSpace->slug,
            files: $codeSpace->files,
            user: $user,
            updated_at: $setUpdatedAt ? $codeSpace->updated_at : null
        );
    }
}
