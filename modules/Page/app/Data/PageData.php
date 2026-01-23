<?php

namespace Modules\Page\Data;

use DateTime;
use Modules\User\Data\UserData;

readonly class PageData
{
    /**
     * @param array{image: string, responsive: string|array<int, string>, srcset: string, thumb: string|null} $image
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly ?string $content,
        public readonly ?string $excerpt,
        public readonly array $image,
        public readonly UserData $user,
        public readonly bool $canUpdate,
        public readonly bool $canDelete,
        public readonly ?DateTime $published_at,
        public readonly string $type = 'page'
    ) {
    }
}
