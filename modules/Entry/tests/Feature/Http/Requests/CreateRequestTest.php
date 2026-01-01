<?php

namespace Modules\Entry\Tests\Feature\Http\Requests;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Modules\Entry\Http\Requests\CreateRequest;
use Modules\Entry\Tests\TestCase;

class CreateRequestTest extends TestCase
{
    public function test_entry_can_be_created_with_valid_content_only(): void
    {
        $data = [
            'content' => str_repeat('a', 50),
        ];

        $request = CreateRequest::create('/entries', 'POST', $data);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_entry_can_be_created_with_media_only(): void
    {
        $data = [
            'media' => [
                UploadedFile::fake()->image('image.jpg'),
            ],
        ];

        $request = CreateRequest::create('/entries', 'POST', $data);
        $request->files->set('media', $data['media']);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_entry_can_be_created_with_content_and_media(): void
    {
        $data = [
            'content' => str_repeat('a', 100),
            'media'   => [
                UploadedFile::fake()->image('image.jpg'),
            ],
        ];

        $request = CreateRequest::create('/entries', 'POST', $data);
        $request->files->set('media', $data['media']);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_entry_requires_content_or_media(): void
    {
        $this->assertValidationError(
            data: [],
            field: 'media'
        );
    }

    public function test_content_must_be_string(): void
    {
        $this->assertValidationError(
            data: ['content' => 123],
            field: 'content'
        );
    }

    public function test_content_must_be_min_50_characters_when_no_media(): void
    {
        $this->assertValidationError(
            data: ['content' => str_repeat('a', 49)],
            field: 'content'
        );
    }

    public function test_content_cannot_exceed_350_characters_when_no_media(): void
    {
        $this->assertValidationError(
            data: ['content' => str_repeat('a', 351)],
            field: 'content'
        );
    }

    public function test_content_length_is_not_validated_when_media_exists(): void
    {
        $data = [
            'content' => 'short',
            'media'   => [
                UploadedFile::fake()->image('image.jpg'),
            ],
        ];

        $request = CreateRequest::create('/entries', 'POST', $data);
        $request->files->set('media', $data['media']);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_media_must_have_min_1_item(): void
    {
        $this->assertValidationError(
            data: [
                'content' => str_repeat('a', 100),
                'media'   => [],
            ],
            field: 'media'
        );
    }

    public function test_media_cannot_exceed_10_items(): void
    {
        $this->assertValidationError(
            data: [
                'content' => str_repeat('a', 100),
                'media'   => array_fill(
                    0,
                    11,
                    UploadedFile::fake()->image('image.jpg')
                ),
            ],
            field: 'media'
        );
    }

    public function test_each_media_item_must_be_image(): void
    {
        $this->assertValidationError(
            data: [
                'media' => [
                    UploadedFile::fake()->create('file.pdf'),
                ],
            ],
            field: 'media.0'
        );
    }

    /**
     * @param array<string, mixed> $data
     * @param string $field
     */
    private function assertValidationError(array $data, string $field): void
    {
        $request = CreateRequest::create('/entries', 'POST', $data);

        if (isset($data['media'])) {
            $request->files->set('media', $data['media']);
        }

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());

        $this->assertArrayHasKey(
            $field,
            $validator->errors()->toArray()
        );
    }
}
