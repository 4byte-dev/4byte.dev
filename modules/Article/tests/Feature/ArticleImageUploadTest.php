<?php

namespace Modules\Article\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Article\Models\Article;
use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ArticleImageUploadTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_can_upload_content_images_on_create(): void
    {
        Storage::fake('public');
        Permission::create(['name' => 'create_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('create_article');

        $category = Category::factory()->create();
        $tag      = Tag::factory()->create();

        $image1 = UploadedFile::fake()->image('img1.jpg');
        $image2 = UploadedFile::fake()->image('img2.jpg');

        $placeholder1 = '[[img_1]]';
        $placeholder2 = '[[img_2]]';

        $content = "This is a test article with images. {$placeholder1} and {$placeholder2}. " . str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 20);

        $response = $this->actingAs($user)->postJson(route('api.article.store'), [
            'title'          => 'Test Article With Images',
            'excerpt'        => 'This is an excerpt strictly longer than 100 characters to pass validation rules which is quite annoying but necessary for consistency..............',
            'content'        => $content,
            'published'      => true,
            'categories'     => [$category->slug],
            'tags'           => [$tag->slug],
            'image'          => UploadedFile::fake()->image('cover.jpg'),
            'sources'        => [['url' => 'https://example.com', 'date' => now()->toDateString()]],
            'content_images' => [
                $placeholder1 => $image1,
                $placeholder2 => $image2,
            ],
        ]);

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200);

        $article = Article::first();
        $this->assertNotNull($article);

        $this->assertCount(3, $article->getMedia('*'));
        $this->assertCount(2, $article->getMedia('content'));

        $mediaItems = $article->getMedia('content');
        $url1       = $mediaItems[0]->getUrl();
        $url2       = $mediaItems[1]->getUrl();

        $this->assertStringNotContainsString($placeholder1, $article->content);
        $this->assertStringNotContainsString($placeholder2, $article->content);
        $this->assertStringContainsString($url1, $article->content);
        $this->assertStringContainsString($url2, $article->content);
    }

    public function test_can_upload_content_images_on_update(): void
    {
        Storage::fake('public');
        Permission::firstOrCreate(['name' => 'update_article']);
        $user = User::factory()->create();
        $user->givePermissionTo('update_article');

        $category = Category::factory()->create();
        $tag      = Tag::factory()->create();

        $article = Article::factory()->create(['user_id' => $user->id]);
        $article->categories()->attach($category);
        $article->tags()->attach($tag);

        $image       = UploadedFile::fake()->image('new.jpg');
        $placeholder = '[[img_new]]';
        $content     = "Updated content with {$placeholder}. " . str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 20);

        $response = $this->actingAs($user)->putJson(route('api.article.update', $article->slug), [
            'title'          => 'Updated Title',
            'excerpt'        => 'This is an updated excerpt strictly longer than 100 characters to pass validation rules which is quite annoying but necessary for consistency..............',
            'content'        => $content,
            'published'      => true,
            'categories'     => [$category->slug],
            'tags'           => [$tag->slug],
            'image'          => UploadedFile::fake()->image('cover.jpg'),
            'sources'        => [['url' => 'https://example.com', 'date' => now()->toDateString()]],
            'content_images' => [
                $placeholder => $image,
            ],
        ]);

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200);

        $article->refresh();

        $this->assertCount(1, $article->getMedia('content'));
        $this->assertStringNotContainsString($placeholder, $article->content);
        $this->assertStringContainsString($article->getMedia('content')->first()->getUrl(), $article->content);
    }
}
