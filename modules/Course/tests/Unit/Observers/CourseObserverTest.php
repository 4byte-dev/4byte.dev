<?php

namespace Modules\Course\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Modules\Course\Models\Course;
use Modules\Course\Observers\CourseObserver;
use Modules\Course\Tests\TestCase;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;

class CourseObserverTest extends TestCase
{
    private GorseService|MockInterface $gorse;

    private CourseObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gorse    = Mockery::mock(GorseService::class);
        $this->observer = new CourseObserver($this->gorse);
    }

    public function test_saved_inserts_item_to_gorse_if_published(): void
    {
        $course = Course::factory()->make([
            'status' => 'PUBLISHED',
            'id'     => 1,
        ]);

        $course->setRelation('tags', collect([]));
        $course->setRelation('categories', collect([]));

        $this->gorse->shouldReceive('insertItem')
            ->once()
            ->with(Mockery::type(GorseItem::class));

        $this->observer->saved($course);
    }

    public function test_saved_does_not_insert_if_not_published(): void
    {
        $course = Course::factory()->make([
            'status' => 'DRAFT',
        ]);

        $this->gorse->shouldNotReceive('insertItem');

        $this->observer->saved($course);
    }

    public function test_updated_clears_cache(): void
    {
        $course = Course::factory()->make(['id' => 1]);

        Cache::shouldReceive('forget')
            ->once()
            ->with("course:1");

        $this->observer->updated($course);
    }

    public function test_deleted_removes_from_gorse_and_clears_cache(): void
    {
        $course = Course::factory()->make([
            'id'   => 1,
            'slug' => 'slug',
        ]);

        $this->gorse->shouldReceive('deleteItem')
            ->once()
            ->with("course:1");

        Cache::shouldReceive('forget')->with("course:slug:id");
        Cache::shouldReceive('forget')->with("course:1");
        Cache::shouldReceive('forget')->with("course:1:likes");
        Cache::shouldReceive('forget')->with("course:1:dislikes");

        $this->observer->deleted($course);
    }
}
