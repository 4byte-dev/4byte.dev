<?php

namespace Modules\User\Data;

readonly class UserProfileData
{
    /**
     * @param array<int, string>|null $socials
     * @param array{image: string, responsive: string|array<int, string>, srcset: string} $cover
     */
    public function __construct(
        public readonly ?int $id,
        public readonly ?string $role,
        public readonly ?string $bio,
        public readonly ?string $location,
        public readonly ?string $website,
        public readonly ?array $socials,
        public readonly array $cover,
    ) {
    }
}
