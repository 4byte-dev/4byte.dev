<?php

namespace Modules\Category\Tests\Unit\Data;

use Modules\Category\Data\CategoryProfileData;
use Modules\Category\Tests\TestCase;

class CategoryProfileDataTest extends TestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $data = new CategoryProfileData(
            id: 1,
            description: 'Test Description',
            color: '#ffffff',
        );

        $this->assertSame(1, $data->id);
        $this->assertSame('Test Description', $data->description);
        $this->assertSame('#ffffff', $data->color);
    }
}
