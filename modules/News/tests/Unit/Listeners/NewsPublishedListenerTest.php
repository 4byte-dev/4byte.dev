<?php

namespace Modules\News\Tests\Unit\Listeners;

use Carbon\Carbon;
use Mockery;
use Modules\Category\Models\Category;
use Modules\News\Enums\NewsStatus;
use Modules\News\Events\NewsPublishedEvent;
use Modules\News\Listeners\NewsPublishedListener;
use Modules\News\Models\News;
use Modules\News\Tests\TestCase;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Services\GorseService;
use Modules\Tag\Models\Tag;

class NewsPublishedListenerTest extends TestCase
{
    public function test_listener_inserts_news_to_gorse(): void
    {
        $news = News::factory()->create([
            'status'       => NewsStatus::PUBLISHED,
            'published_at' => now(),
        ]);
        $category = Category::factory()->create();
        $tag      = Tag::factory()->create();

        $news->categories()->attach($category);
        $news->tags()->attach($tag);

        $event = new NewsPublishedEvent($news);

        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('insertItem')
            ->once()
            ->with(Mockery::on(function (GorseItem $item) use ($news, $category, $tag) {
                $expectedLabels = collect(["tag:{$tag->id}"])
                    ->merge(["category:{$category->id}"])
                    ->merge(['news', "user:{$news->user_id}"])
                    ->all();

                $expectedItem = new GorseItem(
                    "news:{$news->id}",
                    ['news', "user:{$news->user_id}"],
                    $expectedLabels,
                    $news->slug,
                    false, // is_hidden is false since status is PUBLISHED
                    Carbon::parse($news->published_at)->toDateTimeString()
                );

                $actualJson   = json_encode($item);
                $expectedJson = json_encode($expectedItem);

                return $actualJson === $expectedJson;
            }));

        $listener = new NewsPublishedListener();
        $listener->handle($event, $gorseService);
    }
}
