<?php

namespace Modules\Page\Mappers;

use Illuminate\Support\Facades\Gate;
use Modules\Page\Data\PageData;
use Modules\Page\Models\Page;
use Modules\User\Data\UserData;

class PageMapper
{
    /**
     * Create a PageData instance from a Page model.
     */
    public static function toData(Page $page, UserData $user, bool $setId = false): PageData
    {
        return new PageData(
            id: $setId ? $page->id : 0,
            title: $page->title,
            slug: $page->slug,
            content: $page->content,
            excerpt: $page->excerpt,
            image: $page->getCoverImage(),
            user: $user,
            canUpdate: Gate::allows('update', $page),
            canDelete: Gate::allows('delete', $page),
            published_at: $page->published_at,
        );
    }
}
