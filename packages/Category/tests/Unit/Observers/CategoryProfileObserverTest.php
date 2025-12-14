<?php

namespace Packages\Category\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\Category\Observers\CategoryProfileObserver;
use Packages\Category\Tests\TestCase;

class CategoryProfileObserverTest extends TestCase
{
    public function test_it_clears_profile_cache_on_update(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('category:1:profile');

        $category     = new Category();
        $category->id = 1;

        $profile = new CategoryProfile();
        $profile->setRelation('category', $category);

        (new CategoryProfileObserver())->updated($profile);
    }

    public function test_it_clears_profile_cache_on_delete(): void
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('category:1:profile');

        $category     = new Category();
        $category->id = 1;

        $profile = new CategoryProfile();
        $profile->setRelation('category', $category);

        (new CategoryProfileObserver())->deleted($profile);
    }
}
