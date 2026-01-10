<?php

namespace Modules\Article\Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Services\SeoService;
use Honeystone\Seo\Contracts\BuildsMetadata;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;
use Modules\Category\Models\Category;
use Modules\Recommend\Services\FeedService;
use Modules\Tag\Models\Tag;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;

class ArticleCrudControllerTest extends TestCase
{
    use WithFaker;

    public function test_it_displays_article_create_page_with_feed_data(): void
    {
        Permission::firstOrCreate(['name' => 'create_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('create_article');
        $this->actingAs($user);

        $mockCategories = [
            ['id' => 1, 'name' => 'PHP', 'slug' => 'php'],
            ['id' => 2, 'name' => 'Laravel', 'slug' => 'laravel'],
        ];

        $mockTags = [
            ['id' => 1, 'name' => 'Coding', 'slug' => 'coding'],
            ['id' => 2, 'name' => 'Testing', 'slug' => 'testing'],
        ];

        $feedService = Mockery::mock(FeedService::class);
        $feedService->shouldReceive('categories')->once()->andReturn($mockCategories);
        $feedService->shouldReceive('tags')->once()->andReturn($mockTags);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');
        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getArticleCreateSEO')->once()->andReturn($metadata);

        $this->app->instance(FeedService::class, $feedService);
        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('article.create'));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Article/Create')
                ->has('topCategories', 2)
                ->where('topCategories.0.name', 'PHP')
                ->where('topCategories.0.slug', 'php')
                ->where('topCategories.1.name', 'Laravel')
                ->has('topTags', 2)
                ->where('topTags.0.name', 'Coding')
                ->where('topTags.0.slug', 'coding')
        );

        $this->assertArrayHasKey('seo', $response->original->getData());
    }

    public function test_it_creates_published_article_with_relations_and_image(): void
    {
        Permission::firstOrCreate(['name' => 'create_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('create_article');
        $this->actingAs($user);

        $category = Category::factory()->create(['name' => 'Backend', 'slug' => 'backend']);
        $tag      = Tag::factory()->create(['name' => 'Security', 'slug' => 'security']);

        $image = UploadedFile::fake()->image('cover.jpg');

        $payload = [
            'title'      => 'Published Title Updated',
            'excerpt'    => $this->faker->paragraph(10),
            'content'    => $this->faker->paragraph(20, true),
            'published'  => true,
            'categories' => [$category->slug],
            'tags'       => [$tag->slug],
            'image'      => $image,
            'sources'    => [
                ['url' => 'https://4byte.dev', 'date' => now()->toDateString()],
            ],
        ];

        $response = $this->postJson(route('api.article.store'), $payload);

        $response->assertOk();

        $this->assertDatabaseHas('articles', [
            'title'   => 'Published Title Updated',
            'status'  => 'PUBLISHED',
            'user_id' => $user->id,
        ]);

        $article = Article::where('title', 'Published Title Updated')->first();

        $this->assertNotEmpty($article->slug);
        $this->assertNotNull($article->published_at);

        $this->assertTrue($article->categories->contains($category));
        $this->assertTrue($article->tags->contains($tag));
    }

    public function test_it_displays_article_edit_page_with_transformed_data(): void
    {
        Permission::firstOrCreate(['name' => 'update_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('update_article');
        $this->actingAs($user);

        $article = Article::factory()->create([
            'user_id'      => $user->id,
            'title'        => 'Legacy Code Refactoring',
            'excerpt'      => 'Refactoring steps.',
            'content'      => 'Deep dive into legacy code.',
            'status'       => 'PUBLISHED',
            'published_at' => now(),
            'sources'      => [
                ['url' => 'https://example.com', 'date' => '2020-01-01 00:00:00'],
            ],
        ]);

        $category = Category::factory()->create(['slug' => 'clean-code']);
        $tag      = Tag::factory()->create(['slug' => 'refactoring']);

        $article->categories()->attach($category);
        $article->tags()->attach($tag);

        $feedService = Mockery::mock(FeedService::class);
        $feedService->shouldReceive('categories')->once()->andReturn([]);
        $feedService->shouldReceive('tags')->once()->andReturn([]);

        $metadata = Mockery::mock(BuildsMetadata::class);
        $metadata->shouldReceive('generate')->once()->andReturn('seo-html');
        $seoService = Mockery::mock(SeoService::class);
        $seoService->shouldReceive('getArticleEditSEO')->once()->andReturn($metadata);

        $this->app->instance(FeedService::class, $feedService);
        $this->app->instance(SeoService::class, $seoService);

        $response = $this->get(route('article.edit', ['article' => $article->slug]));

        $response->assertStatus(Response::HTTP_OK);

        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Article/Edit')
                ->where('slug', $article->slug)

                ->has(
                    'article',
                    fn (Assert $json) => $json
                        ->where('title', 'Legacy Code Refactoring')
                        ->where('excerpt', 'Refactoring steps.')
                        ->where('content', 'Deep dive into legacy code.')
                        ->where('published', true)
                        ->has('categories', 1)
                        ->where('categories.0', 'clean-code')
                        ->has('tags', 1)
                        ->where('tags.0', 'refactoring')
                        ->has('image')
                        ->where('sources', [
                            ['url' => 'https://example.com', 'date' => '2020-01-01 00:00:00'],
                        ])
                )
        );
    }

    public function test_it_updates_article_status_and_regenerates_slug(): void
    {
        Permission::firstOrCreate(['name' => 'update_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('update_article');
        $this->actingAs($user);

        $article = Article::factory()->create([
            'user_id' => $user->id,
            'title'   => 'Draft Title',
            'slug'    => 'draft-title',
            'status'  => 'DRAFT',
        ]);

        $newCategory = Category::factory()->create();
        $newTag      = Tag::factory()->create();

        $newImage = UploadedFile::fake()->image('new-banner.jpg');

        $payload = [
            'title'      => 'Published Title Updated',
            'excerpt'    => $this->faker->paragraph(10),
            'content'    => $this->faker->paragraph(20, true),
            'published'  => true,
            'categories' => [$newCategory->slug],
            'tags'       => [$newTag->slug],
            'image'      => $newImage,
            'sources'    => [
                ['url' => 'https://4byte.dev', 'date' => now()->toDateString()],
            ],
        ];

        $response = $this->putJson(route('api.article.update', $article->slug), $payload);

        $response->assertOk();
        $response->assertJsonStructure(['slug']);

        $article->refresh();

        $this->assertEquals('PUBLISHED', $article->status);
        $this->assertNotNull($article->published_at);

        $this->assertNotEquals('draft-title', $article->slug);

        $this->assertTrue($article->categories->contains($newCategory));
    }

    public function test_it_handles_draft_creation_with_minimal_data(): void
    {
        Permission::firstOrCreate(['name' => 'create_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('create_article');

        $this->actingAs($user);

        $payload = [
            'title'     => 'Just an idea',
            'published' => false,
        ];

        $response = $this->postJson(route('api.article.store'), $payload);

        $response->assertOk();

        $this->assertDatabaseHas('articles', [
            'title'        => 'Just an idea',
            'status'       => 'DRAFT',
            'published_at' => null,
            'content'      => null,
        ]);
    }

    public function test_it_returns_403_if_user_cannot_create_article(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $payload = [
            'title'     => 'Hacked Title',
            'published' => true,
        ];

        $response = $this->postJson(
            route('api.article.store'),
            $payload
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_guest_cannot_access_article_create_page(): void
    {
        $response = $this->get(
            route('article.create'),
        );

        $response->assertStatus(Response::HTTP_FOUND);
    }

    public function test_it_returns_403_if_user_cannot_update_article(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $article = Article::factory()->create();

        $payload = [
            'title'     => 'Hacked Title',
            'published' => true,
        ];

        $response = $this->putJson(
            route('api.article.update', $article->slug),
            $payload
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_guest_cannot_access_article_edit_page(): void
    {
        $article = Article::factory()->create();

        $response = $this->get(
            route('article.edit', ['article' => $article->slug])
        );

        $response->assertStatus(Response::HTTP_FOUND);
    }

    public function test_it_returns_403_if_user_is_not_owner_of_article(): void
    {
        Permission::firstOrCreate(['name' => 'update_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('update_article');
        $this->actingAs($user);

        $otherUser = User::factory()->create();
        $article   = Article::factory()->create([
            'user_id' => $otherUser->id,
            'title'   => 'Draft Title',
            'slug'    => 'draft-title',
            'status'  => 'DRAFT',
        ]);

        $payload = [
            'title'     => 'Published Title Updated',
            'published' => false,
        ];

        $response = $this->putJson(route('api.article.update', $article->slug), $payload);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $article->refresh();

        $this->assertEquals('Draft Title', $article->title);
    }

    public function test_it_delegates_creation_to_action(): void
    {
        Permission::firstOrCreate(['name' => 'create_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('create_article');
        $this->actingAs($user);

        $actionSpy = Mockery::spy(\Modules\Article\Actions\CreateArticleAction::class);
        $this->app->instance(\Modules\Article\Actions\CreateArticleAction::class, $actionSpy);

        $actionSpy->shouldReceive('execute')->andReturn(Article::factory()->create());

        $payload = [
            'title'     => 'Delegation Test',
            'published' => false,
        ];

        $response = $this->postJson(route('api.article.store'), $payload);
        $response->assertOk();

        $actionSpy->shouldHaveReceived('execute')->once();
    }

    public function test_it_delegates_update_to_action(): void
    {
        Permission::firstOrCreate(['name' => 'update_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('update_article');
        $this->actingAs($user);

        $article = Article::factory()->create(['user_id' => $user->id]);

        $actionSpy = Mockery::spy(\Modules\Article\Actions\UpdateArticleAction::class);
        $this->app->instance(\Modules\Article\Actions\UpdateArticleAction::class, $actionSpy);

        $actionSpy->shouldReceive('execute')->andReturn($article);

        $payload = [
            'title'     => 'Delegation Update',
            'published' => false,
        ];

        $response = $this->putJson(route('api.article.update', $article->slug), $payload);
        $response->assertOk();

        $actionSpy->shouldHaveReceived('execute')->once();
    }
}
