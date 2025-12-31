<?php

namespace Modules\CodeSpace\Tests\Unit\Models;

use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Tests\TestCase;
use Modules\User\Models\User;
use Spatie\Activitylog\Models\Activity;

class CodeSpaceTest extends TestCase
{
    public function test_it_has_fillable_attributes(): void
    {
        $codeSpace = new CodeSpace();
        $this->assertEquals([
            'name',
            'slug',
            'files',
            'user_id',
        ], $codeSpace->getFillable());
    }

    public function test_it_casts_attributes(): void
    {
        $codeSpace = new CodeSpace();

        $this->assertEquals('array', $codeSpace->getCasts()['files']);
    }

    public function test_it_belongs_to_user(): void
    {
        $user      = User::factory()->create();
        $codeSpace = CodeSpace::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $codeSpace->user);
        $this->assertEquals($user->id, $codeSpace->user->id);
    }

    public function test_it_logs_activity_on_create(): void
    {
        CodeSpace::factory()->create([
            'name' => 'CodeSpace Name',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('codespace', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame('CodeSpace Name', $activity->properties['attributes']['name']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $codeSpace = CodeSpace::factory()->create([
            'name' => 'Old Name',
        ]);

        $codeSpace->update(['name' => 'New Name']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame('New Name', $activity->properties['attributes']['name']);
        $this->assertSame('Old Name', $activity->properties['old']['name']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $codeSpace = CodeSpace::factory()->create();

        $initialCount = Activity::count();

        $codeSpace->update(['name' => $codeSpace->name]);

        $this->assertSame($initialCount, Activity::count());
    }
}
