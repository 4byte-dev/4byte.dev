<?php

namespace Modules\User\Tests\Unit\Jobs;

use Modules\User\Jobs\DeleteAccountJob;
use Modules\User\Models\User;
use Modules\User\Tests\TestCase;

class DeleteAccountJobTest extends TestCase
{
    public function test_it_deletes_the_user_when_job_is_handled(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $job = new DeleteAccountJob($user);
        $job->handle();

        $this->assertDatabaseMissing('users', [
            'id'         => $user->id,
            'deleted_at' => null,
        ]);
    }
}
