<?php

namespace Modules\React\Tests\Unit\Observers;

use Mockery;
use Modules\React\Models\Dislike;
use Modules\React\Observers\DislikeObserver;
use Modules\Recommend\Services\GorseService;
use Tests\TestCase;

class DislikeObserverTest extends TestCase
{
    public function test_dislike_created_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('insertFeedback')->once();

        $observer = new DislikeObserver($gorseMock);
        $dislike  = Dislike::factory()->make();
        $dislike->setRelation('dislikeable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->created($dislike);
    }

    public function test_dislike_deleted_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('deleteFeedback')->once();

        $observer = new DislikeObserver($gorseMock);
        $dislike  = Dislike::factory()->make();
        $dislike->setRelation('dislikeable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->deleted($dislike);
    }
}
