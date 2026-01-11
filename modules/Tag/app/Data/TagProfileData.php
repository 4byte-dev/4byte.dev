<?php

namespace Modules\Tag\Data;

use Modules\Category\Data\CategoryData;

readonly class TagProfileData
{
    /**
     * Summary of __construct.
     *
     * @param int|null $id
     * @param string $description
     * @param string $color
     * @param array<int, CategoryData> $categories
     */
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly string $color,
        public readonly array $categories,
    ) {
    }
}
