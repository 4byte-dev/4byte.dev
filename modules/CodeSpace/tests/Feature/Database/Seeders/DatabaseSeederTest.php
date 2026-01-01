<?php

namespace Modules\CodeSpace\Tests\Feature\Database\Seeders;

use Modules\CodeSpace\Database\Seeders\CodeSpaceSeeder;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    public function test_it_seeds_codespaces(): void
    {
        $this->seed(CodeSpaceSeeder::class);

        $this->assertDatabaseCount(CodeSpace::class, 5);
    }
}
