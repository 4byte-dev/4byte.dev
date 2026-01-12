<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;
use Modules\Recommend\Services\GorseService;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Cache::flush();

        $this->app->bind(GorseService::class, function () {
            $userMock = Mockery::mock(\Modules\Recommend\Classes\GorseUser::class);
            $userMock->shouldIgnoreMissing();

            $rowEffectedMock = Mockery::mock(\Modules\Recommend\Classes\RowAffected::class);
            $rowEffectedMock->shouldIgnoreMissing();

            $gorseItemMock = Mockery::mock(\Modules\Recommend\Classes\GorseItem::class);
            $gorseItemMock->shouldIgnoreMissing();

            $gorseServiceMock = Mockery::mock(GorseService::class);
            $gorseServiceMock->shouldReceive('insertUser')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('updateUser')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('getUser')->andReturn($userMock);
            $gorseServiceMock->shouldReceive('deleteUser')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('getItem')->andReturn($gorseItemMock);
            $gorseServiceMock->shouldReceive('insertItem')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('updateItem')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('deleteItem')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('insertItemCategory')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('deleteItemCategory')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('insertFeedback')->andReturn($rowEffectedMock);
            $gorseServiceMock->shouldReceive('deleteFeedback')->andReturn($rowEffectedMock);

            return $gorseServiceMock;
        });
    }
}
