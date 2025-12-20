<?php

namespace Packages\Article\Tests\Feature\Http\Requests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Packages\Article\Http\Requests\CreateRequest;
use Packages\Article\Models\Article;
use Packages\Article\Tests\TestCase;

class CreateRequestTest extends TestCase
{
    public function test_draft_article_requires_only_title(): void
    {
        $data = [
            'title'     => 'This is a valid article title',
            'published' => false,
        ];

        $request = CreateRequest::create('/articles', 'POST', $data);

        $validator = Validator::make($request->all(), (new CreateRequest())->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_published_article_requires_all_fields(): void
    {
        $data = [
            'title'       => 'This is a valid article title',
            'published'   => true,
            'excerpt'     => 'Short',
            'content'     => 'Content',
            'categories'  => [],
            'tags'        => [],
            'sources'     => [],
        ];

        $request = CreateRequest::create('/articles', 'POST', $data);

        $validator = Validator::make($request->all(), (new CreateRequest())->rules());

        $this->assertTrue($validator->fails());

        $errors = $validator->errors();

        $this->assertArrayHasKey('excerpt', $errors->toArray());
        $this->assertArrayHasKey('content', $errors->toArray());
        $this->assertArrayHasKey('categories', $errors->toArray());
        $this->assertArrayHasKey('tags', $errors->toArray());
        $this->assertArrayHasKey('image', $errors->toArray());
        $this->assertArrayHasKey('sources', $errors->toArray());
    }

    public function test_published_article_passes_with_valid_data(): void
    {
        $data = $this->publishedBaseData();

        $request = CreateRequest::create('/articles', 'POST', $data);
        $request->files->set('image', $data['image']);

        $validator = Validator::make($request->all(), (new CreateRequest())->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_it_generates_unique_slug(): void
    {
        Article::create([
            'title' => 'Test Article',
            'slug'  => 'test-article',
        ]);

        $request = CreateRequest::create('/articles', 'POST', [
            'title' => 'Test Article',
        ]);

        $slug = $request->createSlug();

        $this->assertEquals('test-article-1', $slug);
    }

    public function test_it_ignores_given_id_when_generating_slug(): void
    {
        $article = Article::create([
            'title' => 'Test Article',
            'slug'  => 'test-article',
        ]);

        $request = CreateRequest::create('/articles', 'POST', [
            'title' => 'Test Article',
        ]);

        $slug = $request->createSlug($article->id);

        $this->assertEquals('test-article', $slug);
    }

    public function test_title_is_required(): void
    {
        $this->assertValidationError(
            data: ['published' => false],
            field: 'title'
        );
    }

    public function test_title_must_be_string(): void
    {
        $this->assertValidationError(
            data: ['title' => 123, 'published' => false],
            field: 'title'
        );
    }

    public function test_title_must_be_min_10_characters(): void
    {
        $this->assertValidationError(
            data: ['title' => 'short', 'published' => false],
            field: 'title'
        );
    }

    public function test_excerpt_is_required_when_published(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData(except: ['excerpt']),
            field: 'excerpt'
        );
    }

    public function test_excerpt_must_be_min_100_characters(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'excerpt' => str_repeat('a', 99),
            ]),
            field: 'excerpt'
        );
    }

    public function test_content_is_required_when_published(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData(except: ['content']),
            field: 'content'
        );
    }

    public function test_content_must_be_min_500_characters(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'content' => str_repeat('a', 499),
            ]),
            field: 'content'
        );
    }

    public function test_categories_is_required_when_published(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData(except: ['categories']),
            field: 'categories'
        );
    }

    public function test_categories_must_be_array(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'categories' => 'php',
            ]),
            field: 'categories'
        );
    }

    public function test_categories_must_have_min_1_item(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'categories' => [],
            ]),
            field: 'categories'
        );
    }

    public function test_categories_cannot_exceed_3_items(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'categories' => ['a', 'b', 'c', 'd'],
            ]),
            field: 'categories'
        );
    }

    public function test_tags_is_required_when_published(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData(except: ['tags']),
            field: 'tags'
        );
    }

    public function test_tags_cannot_exceed_3_items(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'tags' => ['a', 'b', 'c', 'd'],
            ]),
            field: 'tags'
        );
    }

    public function test_image_is_required_when_published(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData(except: ['image']),
            field: 'image'
        );
    }

    public function test_image_must_be_image_file(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'image' => UploadedFile::fake()->create('file.pdf'),
            ]),
            field: 'image'
        );
    }

    public function test_sources_is_required_when_published(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData(except: ['sources']),
            field: 'sources'
        );
    }

    public function test_sources_url_must_be_valid_url(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'sources' => [
                    'url'  => 'not-a-url',
                    'date' => now()->toDateString(),
                ],
            ]),
            field: 'sources.url'
        );
    }

    public function test_sources_date_must_be_valid_date(): void
    {
        $this->assertValidationError(
            data: $this->publishedBaseData([
                'sources' => [
                    'url'  => 'https://example.com',
                    'date' => 'invalid-date',
                ],
            ]),
            field: 'sources.date'
        );
    }

    /**
     * @param array<string, mixed> $override
     * @param array<int, string> $except
     *
     * @return array{categories: string[], content: string, excerpt: string, image: \Illuminate\Http\Testing\File, published: bool, sources: array{date: string, url: string, tags: string[], title: string}}
     */
    private function publishedBaseData(array $override = [], array $except = []): array
    {
        $data = [
            'title'       => 'This is a valid article title',
            'published'   => true,
            'excerpt'     => str_repeat('a', 100),
            'content'     => str_repeat('b', 500),
            'categories'  => ['php'],
            'tags'        => ['laravel'],
            'image'       => UploadedFile::fake()->image('image.jpg'),
            'sources'     => [
                'url'  => 'https://example.com',
                'date' => now()->toDateString(),
            ],
        ];

        foreach ($except as $key) {
            unset($data[$key]);
        }

        return array_merge($data, $override);
    }

    /**
     * @param array<string, mixed> $data
     * @param string $field
     */
    private function assertValidationError(array $data, string $field): void
    {
        $request = CreateRequest::create('/articles', 'POST', $data);

        if (isset($data['image'])) {
            $request->files->set('image', $data['image']);
        }

        $validator = Validator::make($request->all(), (new CreateRequest())->rules());

        $this->assertTrue(
            $validator->fails(),
            "Validation should fail for field: {$field}"
        );

        $this->assertArrayHasKey(
            $field,
            $validator->errors()->toArray(),
            "Expected validation error for field: {$field}"
        );
    }
}
