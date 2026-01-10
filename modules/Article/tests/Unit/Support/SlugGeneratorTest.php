<?php

namespace Modules\Article\Tests\Unit\Support;

use Modules\Article\Models\Article;
use Modules\Article\Support\SlugGenerator;
use Modules\Article\Tests\TestCase;

class SlugGeneratorTest extends TestCase
{
    private SlugGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new SlugGenerator();
    }

    public function test_it_generates_basic_slug(): void
    {
        $slug = $this->generator->generate('Hello World');
        $this->assertEquals('hello-world', $slug);
    }

    public function test_it_handles_special_characters(): void
    {
        $slug = $this->generator->generate('Hello & World @ 2026');
        $this->assertEquals('hello-world-at-2026', $slug);
    }

    public function test_it_appends_counter_for_existing_slugs(): void
    {
        Article::factory()->create(['slug' => 'existing-title']);

        $slug = $this->generator->generate('Existing Title');
        $this->assertEquals('existing-title-1', $slug);

        Article::factory()->create(['slug' => 'existing-title-1']);
        $slug = $this->generator->generate('Existing Title');
        $this->assertEquals('existing-title-2', $slug);
    }

    public function test_it_ignores_current_id_during_update(): void
    {
        $article = Article::factory()->create(['slug' => 'my-title']);

        $slug = $this->generator->generate('My Title', $article->id);
        $this->assertEquals('my-title', $slug);

        Article::factory()->create(['slug' => 'my-title-1']);
        $slug = $this->generator->generate('My Title 1', $article->id);
        $this->assertEquals('my-title-1-1', $slug);
    }
}
