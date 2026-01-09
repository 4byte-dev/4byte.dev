<?php

namespace Modules\User\Tests\Unit\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\User\Http\Requests\ProfileUpdateRequest;
use Modules\User\Tests\TestCase;

class ProfileUpdateRequestTest extends TestCase
{
    public function test_name_is_required(): void
    {
        $this->assertFails(
            data: ['email' => 'test@example.com'],
            field: 'name'
        );
    }

    public function test_name_must_be_string(): void
    {
        $this->assertFails(
            data: ['name' => 123, 'email' => 'test@example.com'],
            field: 'name'
        );
    }

    public function test_email_is_required(): void
    {
        $this->assertFails(
            data: ['name' => 'John Doe'],
            field: 'email'
        );
    }

    public function test_email_must_be_valid(): void
    {
        $this->assertFails(
            data: ['name' => 'John Doe', 'email' => 'not-an-email'],
            field: 'email'
        );
    }

    public function test_email_must_be_unique_except_current_user(): void
    {
        User::factory()->create([
            'email' => 'taken@example.com',
        ]);

        $currentUser = User::factory()->create([
            'email' => 'current@example.com',
        ]);

        $this->assertFails(
            data: [
                'name'  => 'John Doe',
                'email' => 'taken@example.com',
            ],
            field: 'email',
            user: $currentUser
        );
    }

    public function test_user_can_keep_own_email(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $this->assertPasses(
            data: [
                'name'  => 'Updated Name',
                'email' => 'user@example.com',
            ],
            user: $user
        );
    }

    public function test_validation_passes_with_valid_data(): void
    {
        $user = User::factory()->create();

        $this->assertPasses(
            data: [
                'name'  => 'John Doe',
                'email' => 'john@example.com',
            ],
            user: $user
        );
    }

    private function makeValidator(array $data, ?User $user = null)
    {
        $request = new ProfileUpdateRequest();

        if ($user) {
            $request->setUserResolver(fn () => $user);
        }

        return Validator::make($data, $request->rules());
    }

    private function assertFails(array $data, string $field, ?User $user = null): void
    {
        $validator = $this->makeValidator($data, $user);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($field, $validator->errors()->toArray());
    }

    private function assertPasses(array $data, ?User $user = null): void
    {
        $validator = $this->makeValidator($data, $user);

        $this->assertFalse($validator->fails());
    }
}
