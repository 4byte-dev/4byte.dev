<?php

namespace Modules\Tag\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Tag\Models\TagProfile;
use Modules\Tag\Observers\TagProfileObserver;
use Modules\Tag\Tests\TestCase;

class TagProfileObserverTest extends TestCase
{
    public function test_it_clears_profile_cache_on_update(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('tag:1:profile');

        $profile     = new TagProfile();
        $profile->id = 1;

        (new TagProfileObserver())->updated($profile);
    }

    public function test_it_clears_profile_cache_on_delete(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('tag:1:profile');

        $profile     = new TagProfile();
        $profile->id = 1;

        (new TagProfileObserver())->deleted($profile);
    }
}
