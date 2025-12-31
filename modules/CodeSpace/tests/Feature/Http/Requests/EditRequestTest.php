<?php

namespace Modules\CodeSpace\Tests\Feature\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Modules\CodeSpace\Http\Requests\EditRequest;
use Modules\CodeSpace\Tests\TestCase;

class EditRequestTest extends TestCase
{
    public function test_it_authorizes_request(): void
    {
        $request = new EditRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_it_has_valid_rules(): void
    {
        $request = new EditRequest();
        $rules   = $request->rules();

        $data = [
            'name'  => 'Valid Name',
            'files' => [
                [
                    'name'     => 'test.js',
                    'language' => 'javascript',
                    'content'  => 'alert(1);',
                ],
            ],
        ];

        $validator = Validator::make($data, $rules);
        $this->assertTrue($validator->passes());
    }

    public function test_it_fails_validation(): void
    {
        $request = new EditRequest();
        $rules   = $request->rules();

        $data = [
            'name' => 'sho',
        ];

        $validator = Validator::make($data, $rules);
        $this->assertFalse($validator->passes());
        $this->assertTrue($validator->errors()->has('name'));
        $this->assertTrue($validator->errors()->has('files'));
    }
}
