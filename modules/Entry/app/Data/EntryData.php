<?php

namespace Modules\Entry\Data;

use DateTime;
use Modules\User\Data\UserData;

readonly class EntryData
{
    /**
     * @param ?array<int, array{image: string, responsive: string|array<int, string>, srcset: string}> $media
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $slug,
        public readonly ?string $content,
        public readonly ?array $media,
        public readonly UserData $user,
        public readonly int $likes,
        public readonly int $dislikes,
        public readonly int $comments,
        public readonly bool $isLiked,
        public readonly bool $isDisliked,
        public readonly bool $isSaved,
        public readonly bool $canUpdate,
        public readonly bool $canDelete,
        public readonly ?DateTime $published_at,
        public readonly string $type = 'entry'
    ) {
    }
}
