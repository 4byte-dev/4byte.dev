<?php

namespace Modules\Course\Data;

use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Modules\Category\Data\CategoryData;
use Modules\Category\Mappers\CategoryMapper;
use Modules\Course\Models\Course;
use Modules\Tag\Data\TagData;
use Modules\Tag\Mappers\TagMapper;
use Modules\User\Data\UserData;
use Spatie\LaravelData\Data;

class CourseData extends Data
{
    /**
     * @param array{image: string, responsive: string|array<int, string>, srcset: string, thumb: string|null} $image
     * @param array<CategoryData> $categories
     * @param array<TagData> $tags
     */
    public function __construct(
        public ?int $id,
        public string $title,
        public string $slug,
        public string $difficulty,
        public ?string $excerpt,
        public ?string $content,
        public array $image,
        public ?DateTime $published_at,
        public UserData $user,
        public array $categories,
        public array $tags,
        public int $likes,
        public int $dislikes,
        public int $comments,
        public bool $isLiked,
        public bool $isDisliked,
        public bool $isSaved,
        public bool $canUpdate,
        public bool $canDelete,
        public string $type = 'course'
    ) {
    }

    /**
     * Create a TagData instance from a Tag model.
     */
    public static function fromModel(Course $course, UserData $user, bool $setId = false): self
    {
        $userId = Auth::id();

        return new self(
            id: $setId ? $course->id : 0,
            title: $course->title,
            slug: $course->slug,
            difficulty: $course->difficulty,
            content: $course->content,
            excerpt: $course->excerpt,
            image: $course->getCoverImage(),
            published_at: $course->published_at,
            user: $user,
            categories: CategoryMapper::collection($course->categories),
            tags: TagMapper::collection($course->tags),
            likes: $course->likesCount(),
            dislikes: $course->dislikesCount(),
            comments: $course->commentsCount(),
            isLiked: $course->isLikedBy($userId),
            isDisliked: $course->isDislikedBy($userId),
            isSaved: $course->isSavedBy($userId),
            canUpdate: Gate::allows('update', $course),
            canDelete: Gate::allows('delete', $course)
        );
    }
}
