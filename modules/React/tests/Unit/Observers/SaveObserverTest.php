<?php

namespace Modules\React\Tests\Unit\Observers;

use Mockery;
use Modules\React\Models\Save;
use Modules\React\Observers\SaveObserver;
use Modules\Recommend\Services\GorseService;
use Tests\TestCase;

class SaveObserverTest extends TestCase
{
    public function test_save_created_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('insertFeedback')->once();

        $observer = new SaveObserver($gorseMock);
        $save     = Save::factory()->make();
        $save->setRelation('saveable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->created($save);
    }

    public function test_save_deleted_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('deleteFeedback')->once();

        $observer = new SaveObserver($gorseMock);
        $save     = Save::factory()->make();
        $save->setRelation('saveable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->deleted($save);
    }
}
