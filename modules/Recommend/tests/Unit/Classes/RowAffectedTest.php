<?php

namespace Modules\Recommend\Tests\Unit\Classes;

use Modules\Recommend\Classes\RowAffected;
use Modules\Recommend\Tests\TestCase;

class RowAffectedTest extends TestCase
{
    public function test_hydration_from_json(): void
    {
        $affected = RowAffected::fromJSON([
            'RowAffected' => 5,
        ]);

        $this->assertSame(5, $affected->getRowAffected());
    }
}
