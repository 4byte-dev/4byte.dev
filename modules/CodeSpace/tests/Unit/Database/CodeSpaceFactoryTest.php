<?php

namespace Modules\CodeSpace\Tests\Unit\Database;

use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Tests\TestCase;

class CodeSpaceFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_codespace(): void
    {
        $codeSpace = CodeSpace::factory()->create();

        $this->assertDatabaseHas(CodeSpace::class, [
            'id' => $codeSpace->id,
        ]);
        $this->assertInstanceOf(CodeSpace::class, $codeSpace);
    }
}
