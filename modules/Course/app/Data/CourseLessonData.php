<?php

namespace Modules\Course\Data;

use DateTime;

readonly class CourseLessonData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly ?string $content,
        public readonly ?string $video_url,
        public readonly ?DateTime $published_at,
        public readonly bool $isSaved,
        public readonly bool $canUpdate,
        public readonly bool $canDelete,
        public readonly string $type = 'lesson'
    ) {
    }
}
