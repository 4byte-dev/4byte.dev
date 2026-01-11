<?php

namespace Modules\Category\Data;

readonly class CategoryProfileData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $description,
        public readonly string $color,
    ) {
    }
}
