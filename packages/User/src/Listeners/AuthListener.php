<?php

namespace Packages\User\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Auth\Events\Registered;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Packages\User\Models\User;
use Spatie\Activitylog\Facades\Activity;

class AuthListener implements ShouldQueue
{
    use Queueable;

    /**
     * Handle the event.
     */
    public function handle(Login|Registered|Logout|PasswordResetLinkSent|PasswordReset $event): void
    {
        /** @var User|null $user */
        $user = $event->user instanceof User ? $event->user : null;

        Activity::causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->event(class_basename($event))
            ->log(class_basename($event));
    }
}
