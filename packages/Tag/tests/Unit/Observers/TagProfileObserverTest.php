<?php

namespace Packages\Tag\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Tag\Models\TagProfile;
use Packages\Tag\Observers\TagProfileObserver;
use Packages\Tag\Tests\TestCase;

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
