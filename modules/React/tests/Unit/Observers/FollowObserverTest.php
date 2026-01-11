<?php

namespace Modules\React\Tests\Unit\Observers;

use Mockery;
use Modules\React\Models\Follow;
use Modules\React\Observers\FollowObserver;
use Modules\Recommend\Services\GorseService;
use Tests\TestCase;

class FollowObserverTest extends TestCase
{
    public function test_follow_created_observer_sends_feedback_to_gorse(): void
    {
        $gorseUserMock = Mockery::mock(\Modules\Recommend\Classes\GorseUser::class);
        $gorseUserMock->shouldReceive('addLabel')->once();

        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('getUser')->once()->andReturn($gorseUserMock);
        $gorseMock->shouldReceive('updateUser')->once()->with($gorseUserMock);

        $observer = new FollowObserver($gorseMock);
        $follow   = Follow::factory()->make();
        $follow->setRelation('followable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->created($follow);
    }

    public function test_follow_deleted_observer_sends_feedback_to_gorse(): void
    {
        $gorseUserMock = Mockery::mock(\Modules\Recommend\Classes\GorseUser::class);
        $gorseUserMock->shouldReceive('removeLabel')->once();

        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('getUser')->once()->andReturn($gorseUserMock);
        $gorseMock->shouldReceive('updateUser')->once()->with($gorseUserMock);

        $observer = new FollowObserver($gorseMock);
        $follow   = Follow::factory()->make();
        $follow->setRelation('followable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->deleted($follow);
    }
}
