<?php

namespace Modules\React\Data;

use DateTime;
use Modules\User\Data\UserData;

readonly class CommentData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $content,
        public readonly ?int $parent,
        public readonly DateTime $published_at,
        public readonly UserData $user,
        public readonly int $replies,
        public readonly int $likes,
        public readonly bool $isLiked,
        public readonly ?string $content_type = null,
        public readonly ?string $content_title = null,
        public readonly ?string $content_slug = null,
        public readonly string $type = 'comment'
    ) {
    }
}
