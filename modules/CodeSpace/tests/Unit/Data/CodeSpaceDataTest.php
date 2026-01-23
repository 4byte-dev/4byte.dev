<?php

namespace Modules\CodeSpace\Tests\Unit\Data;

use Modules\CodeSpace\Mappers\CodeSpaceMapper;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Tests\TestCase;
use Modules\User\Data\UserData;
use Modules\User\Mappers\UserMapper;
use Modules\User\Models\User;

class CodeSpaceDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $codeSpace = CodeSpace::factory()->make([
            'id'    => 1,
            'files' => [['name' => 'index.js', 'content' => '']],
        ]);

        $userData = new UserData(
            id: 1,
            name: 'User',
            username: 'user',
            avatar: '',
            followers: 0,
            followings: 0,
            isFollowing: false,
            created_at: now()
        );

        $data = CodeSpaceMapper::toData($codeSpace, $userData, true, true);

        $this->assertEquals($codeSpace->id, $data->id);
        $this->assertEquals($codeSpace->name, $data->name);
        $this->assertEquals($codeSpace->slug, $data->slug);
        $this->assertIsArray($data->files);
        $this->assertEquals($userData, $data->user);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $codeSpace = CodeSpace::factory()->make([
            'id'    => 1,
            'files' => [['name' => 'index.js', 'content' => '']],
        ]);

        $userData = new UserData(
            id: 1,
            name: 'User',
            username: 'user',
            avatar: '',
            followers: 0,
            followings: 0,
            isFollowing: false,
            created_at: now()
        );

        $data = CodeSpaceMapper::toData($codeSpace, $userData);

        $this->assertEquals(0, $data->id);
        $this->assertEquals($codeSpace->name, $data->name);
        $this->assertEquals($codeSpace->slug, $data->slug);
        $this->assertIsArray($data->files);
        $this->assertEquals($userData, $data->user);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $codeSpace = CodeSpace::factory()->create();

        $user = User::factory()->create();

        $userData = UserMapper::toData($user);

        $data = CodeSpaceMapper::toData($codeSpace, $userData, true);

        $this->assertEquals($codeSpace->id, $data->id);
    }
}
