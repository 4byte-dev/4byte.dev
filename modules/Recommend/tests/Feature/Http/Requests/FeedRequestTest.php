<?php

namespace Modules\Recommend\Tests\Feature\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Modules\Recommend\Http\Requests\FeedRequest;
use Modules\Recommend\Tests\TestCase;

class FeedRequestTest extends TestCase
{
    public function test_request_passes_with_no_parameters(): void
    {
        $request = FeedRequest::create('/feed', 'GET', []);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_page_must_be_integer(): void
    {
        $this->assertValidationError(
            data: ['page' => 'abc'],
            field: 'page'
        );
    }

    public function test_page_must_be_min_1(): void
    {
        $this->assertValidationError(
            data: ['page' => 0],
            field: 'page'
        );
    }

    public function test_page_passes_with_valid_value(): void
    {
        $request = FeedRequest::create('/feed', 'GET', [
            'page' => 1,
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    public function test_string_fields_must_be_string(): void
    {
        $fields = [
            'tab',
            'tag',
            'category',
            'article',
            'entry',
            'user',
        ];

        foreach ($fields as $field) {
            $this->assertValidationError(
                data: [$field => 123],
                field: $field
            );
        }
    }

    public function test_request_passes_with_all_valid_parameters(): void
    {
        $data = [
            'page'     => 2,
            'tab'      => 'popular',
            'tag'      => 'laravel',
            'category' => 'backend',
            'article'  => 'test-article',
            'entry'    => 'homepage',
            'user'     => 'omer',
        ];

        $request = FeedRequest::create('/feed', 'GET', $data);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertFalse($validator->fails());
    }

    /**
     * @param array<string, mixed> $data
     * @param string $field
     */
    private function assertValidationError(array $data, string $field): void
    {
        $request = FeedRequest::create('/feed', 'GET', $data);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($field, $validator->errors()->toArray());
    }
}
