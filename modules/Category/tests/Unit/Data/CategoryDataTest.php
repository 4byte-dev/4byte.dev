<?php

namespace Modules\Category\Tests\Unit\Data;

use Modules\Category\Data\CategoryData;
use Modules\Category\Tests\TestCase;

class CategoryDataTest extends TestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $data = new CategoryData(
            id: 1,
            name: 'Test Category',
            slug: 'test-category',
            followers: 10,
            isFollowing: true
        );

        $this->assertSame(1, $data->id);
        $this->assertSame('Test Category', $data->name);
        $this->assertSame('test-category', $data->slug);
        $this->assertSame(10, $data->followers);
        $this->assertTrue($data->isFollowing);

        $this->assertSame('category', $data->type);
    }
}
