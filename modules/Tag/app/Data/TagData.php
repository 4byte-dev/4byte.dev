<?php

namespace Modules\Tag\Data;

readonly class TagData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly int $followers,
        public readonly bool $isFollowing,
        public readonly string $type = 'tag'
    ) {
    }
}
