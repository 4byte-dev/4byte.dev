<?php

namespace Modules\React\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\React\Services\ReactService;
use Modules\React\Tests\TestCase;
use Modules\React\Traits\HasCounts;

/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class TestModelForCounts extends Model
{
    use HasCounts;

    protected $table = 'test_models';

    protected $guarded = [];
}

class HasCountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! \Schema::hasTable('test_models')) {
            \Schema::create('test_models', function ($table) {
                $table->id();
                $table->timestamps();
            });
        }

        $this->artisan('migrate');
        Cache::flush();
    }

    public function test_increment_count_creates_record_and_cache_with_trait(): void
    {
        $model = TestModelForCounts::create();

        $model->incrementCount('views');

        $this->assertDatabaseHas('counts', [
            'countable_type' => TestModelForCounts::class,
            'countable_id'   => $model->id,
            'filter'         => 'views',
            'count'          => 1,
        ]);

        $this->assertEquals(1, Cache::get("react:counts:testmodelforcounts:{$model->id}:views"));
    }

    public function test_decrement_count_with_trait(): void
    {
        $model = TestModelForCounts::create();
        $model->incrementCount('views');
        $model->incrementCount('views');

        $model->decrementCount('views');

        $this->assertDatabaseHas('counts', [
            'count'  => 1,
            'filter' => 'views',
        ]);

        $this->assertEquals(1, Cache::get("react:counts:testmodelforcounts:{$model->id}:views"));
    }

    public function test_get_count_with_trait(): void
    {
        $model = TestModelForCounts::create();

        app(ReactService::class)->incrementCount($model->getMorphClass(), $model->getKey(), 'downloads', 100);

        $this->assertEquals(100, $model->getCount('downloads'));
    }
}
