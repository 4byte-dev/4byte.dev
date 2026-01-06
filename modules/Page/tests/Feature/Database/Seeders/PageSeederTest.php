<?php

namespace Modules\Page\Tests\Feature\Database\Seeders;

use Modules\Page\Database\Seeders\PageSeeder;
use Modules\Page\Models\Page;
use Modules\Page\Tests\TestCase;

class PageSeederTest extends TestCase
{
    public function test_it_seeds_pages(): void
    {
        $this->seed(PageSeeder::class);

        $this->assertDatabaseCount(Page::class, 10);
    }
}
