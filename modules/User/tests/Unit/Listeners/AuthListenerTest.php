<?php

namespace Modules\User\Tests\Unit\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Modules\User\Listeners\AuthListener;
use Modules\User\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class AuthListenerTest extends TestCase
{
    public function test_listener_logs_login_activity(): void
    {
        $user = User::factory()->create();

        $event    = new Login('web', $user, false);
        $listener = new AuthListener();

        $listener->handle($event);

        $this->assertDatabaseHas('activity_log', [
            'log_name'     => 'default',
            'event'        => 'Login',
            'subject_type' => User::class,
            'subject_id'   => $user->id,
            'causer_type'  => User::class,
            'causer_id'    => $user->id,
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertEquals('Login', $activity->event);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertArrayHasKey('ip', $activity->properties->toArray());
        $this->assertArrayHasKey('user_agent', $activity->properties->toArray());
    }
}
