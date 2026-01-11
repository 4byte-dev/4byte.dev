<?php

namespace Modules\Tag\Tests\Unit\Mappers;

use Modules\Tag\Mappers\TagMapper;
use Modules\Tag\Models\Tag;
use Modules\Tag\Tests\TestCase;

class TagMapperTest extends TestCase
{
    public function test_to_data_converts_model_to_data(): void
    {
        $tag = Tag::factory()->create();

        $data = TagMapper::toData($tag, true);

        $this->assertEquals($tag->id, $data->id);
        $this->assertEquals($tag->name, $data->name);
        $this->assertEquals($tag->slug, $data->slug);
    }

    public function test_to_data_converts_model_without_id_by_default(): void
    {
        $tag = Tag::factory()->create();

        $data = TagMapper::toData($tag);

        $this->assertEquals(0, $data->id);
        $this->assertEquals($tag->name, $data->name);
        $this->assertEquals($tag->slug, $data->slug);
    }
}
