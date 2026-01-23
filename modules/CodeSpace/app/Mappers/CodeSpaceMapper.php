<?php

namespace Modules\CodeSpace\Mappers;

use Modules\CodeSpace\Data\CodeSpaceData;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\User\Data\UserData;

class CodeSpaceMapper
{
    /**
     * Create a CodeSpaceData instance from a CodeSpace model.
     */
    public static function toData(CodeSpace $codeSpace, UserData $user, bool $setId = false, bool $setUpdatedAt = false): CodeSpaceData
    {
        return new CodeSpaceData(
            id: $setId ? $codeSpace->id : 0,
            name: $codeSpace->name,
            slug: $codeSpace->slug,
            files: $codeSpace->files,
            user: $user,
            updated_at: $setUpdatedAt ? $codeSpace->updated_at : null
        );
    }

    /**
     * @param iterable<CodeSpace> $codeSpaces
     * @param bool $setId
     *
     * @return array<CodeSpaceData>
     */
    public static function collection(iterable $codeSpaces, UserData $user, bool $setId = false, bool $setUpdatedAt = false): array
    {
        $data = [];
        foreach ($codeSpaces as $codeSpace) {
            $data[] = self::toData($codeSpace, $user, $setId, $setUpdatedAt);
        }

        return $data;
    }
}
