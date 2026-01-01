<?php

namespace Modules\CodeSpace\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Services\CodeSpaceService;
use Modules\CodeSpace\Tests\TestCase;

class CodeSpaceServiceTest extends TestCase
{
    protected CodeSpaceService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = app(CodeSpaceService::class);
    }

    public function test_get_data_returns_article_data(): void
    {
        $codeSpace = CodeSpace::factory()->create();

        $data = $this->service->getData($codeSpace->id);

        $this->assertEquals($codeSpace->name, $data->name);
        $this->assertEquals($codeSpace->user->username, $data->user->username);
        $this->assertTrue(Cache::has("codespace:{$codeSpace->id}"));
    }

    public function test_it_can_get_codeSpace_id_by_slug(): void
    {
        $codeSpace = CodeSpace::factory()->create();

        $id = $this->service->getId($codeSpace->slug);

        $this->assertEquals($codeSpace->id, $id);
        $this->assertTrue(Cache::has("codespace:{$codeSpace->slug}:id"));
    }
}
