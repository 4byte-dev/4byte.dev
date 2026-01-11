<?php

namespace Modules\React\Tests\Unit\Observers;

use Mockery;
use Modules\React\Models\Like;
use Modules\React\Observers\LikeObserver;
use Modules\Recommend\Services\GorseService;
use Tests\TestCase;

class LikeObserverTest extends TestCase
{
    public function test_like_created_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('insertFeedback')->once();

        $observer = new LikeObserver($gorseMock);
        $like     = Like::factory()->make();
        $like->setRelation('likeable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->created($like);
    }

    public function test_like_deleted_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('deleteFeedback')->once();

        $observer = new LikeObserver($gorseMock);
        $like     = Like::factory()->make();
        $like->setRelation('likeable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->deleted($like);
    }
}
