<?php

namespace Modules\CodeSpace\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\CodeSpace\Models\CodeSpace;
use Modules\CodeSpace\Tests\TestCase;
use Spatie\Permission\Models\Permission;

class CodeSpaceCrudControllerTest extends TestCase
{
    use WithFaker;

    public function test_it_lists_user_codespaces(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        CodeSpace::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('api.codespace.crud.list'));

        $response->assertOk();
        $response->assertJsonCount(3);
    }

    public function test_it_gets_codespace_data(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $codeSpace = CodeSpace::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson(route('api.codespace.crud.get', $codeSpace->slug));

        $response->assertOk();
        $response->assertJson([
            'id'   => 0,
            'name' => $codeSpace->name,
            'slug' => $codeSpace->slug,
        ]);
    }

    public function test_it_creates_codespace(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'create_code::space']);
        $user->givePermissionTo('create_code::space');

        $this->actingAs($user);

        $payload = [
            'name'  => 'New Project',
            'files' => [['name' => 'main.js', 'language' => 'javascript', 'content' => 'print("hello")']],
        ];

        $response = $this->postJson(route('api.codespace.crud.create'), $payload);

        $response->assertOk();
        $response->assertJsonStructure(['slug']);

        $this->assertDatabaseHas('code_spaces', [
            'name'    => 'New Project',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_edits_codespace(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'update_code::space']);
        $user->givePermissionTo('update_code::space');
        $codeSpace = CodeSpace::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $payload = [
            'name'  => 'Updated Project',
            'files' => [['name' => 'main.js', 'language' => 'javascript', 'content' => 'updated content']],
        ];

        $response = $this->postJson(route('api.codespace.crud.edit', $codeSpace->slug), $payload);

        $response->assertOk();

        $codeSpace->refresh();
        $this->assertEquals('Updated Project', $codeSpace->name);
    }

    public function test_it_validates_create_request(): void
    {
        $user = User::factory()->create();
        Permission::create(['name' => 'create_code::space']);
        $user->givePermissionTo('create_code::space');
        $this->actingAs($user);

        $response = $this->postJson(route('api.codespace.crud.create'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'files']);
    }
}
