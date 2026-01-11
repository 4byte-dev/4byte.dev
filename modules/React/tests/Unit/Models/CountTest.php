<?php

namespace Modules\React\Tests\Unit\Models;

use Modules\React\Models\Count;
use Modules\React\Tests\TestCase;

class CountTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $count = new Count();

        $this->assertEquals(
            [
                'countable_id',
                'countable_type',
                'filter',
                'count',
            ],
            $count->getFillable()
        );
    }
}
