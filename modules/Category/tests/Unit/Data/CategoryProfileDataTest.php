<?php

namespace Modules\Category\Tests\Unit\Data;

use Modules\Category\Data\CategoryProfileData;
use Modules\Category\Models\CategoryProfile;
use Modules\Category\Tests\TestCase;

class CategoryProfileDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_valid_data(): void
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

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $profile = CategoryProfile::factory()->create();

        $data = CategoryProfileData::fromModel($profile, true);

        $this->assertSame($profile->id, $data->id);
        $this->assertSame($profile->description, $data->description);
        $this->assertSame($profile->color, $data->color);
    }
}
