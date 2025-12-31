<?php

namespace Modules\CodeSpace\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Observers\CodeSpaceObserver;
use Modules\CodeSpace\Tests\TestCase;

class CodeSpaceObserverTest extends TestCase
{
    private CodeSpaceObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new CodeSpaceObserver();
    }

    public function test_updated_clears_cache(): void
    {
        $codeSpace = CodeSpace::factory()->make(['id' => 1]);

        Cache::shouldReceive('forget')->once()->with('codespace:1');

        $this->observer->updated($codeSpace);
    }

    public function test_delete_clears_all_cache(): void
    {
        $codeSpace = CodeSpace::factory()->make([
            'id'      => 1,
            'slug'    => 'test-slug',
            'user_id' => 10,
        ]);

        Cache::shouldReceive('forget')->once()->with('codespace:test-slug:id');
        Cache::shouldReceive('forget')->once()->with('codespace:1');
        Cache::shouldReceive('forget')->once()->with('codespace:10:codespaces');

        $observer = new CodeSpaceObserver();
        $observer->deleted($codeSpace);
    }
}
