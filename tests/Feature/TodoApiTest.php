<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Domain\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_can_list_todos(): void
    {
        Todo::factory()->count(3)->create();

        $response = $this->getJson(route('v1.todos.index'));

        $response->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'category', 'priority', 'status', 'created_at', 'updated_at'],
                ],
            ]);
    }

    public function test_can_create_todo(): void
    {
        $payload = [
            'title' => 'Neues Todo',
            'category' => 'Work',
            'priority' => 'High',
            'status' => 'Todo',
        ];

        $response = $this->postJson(route('v1.todos.store'), $payload);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Neues Todo')
            ->assertJsonPath('data.status', 'Todo');
    }

    public function test_can_update_todo(): void
    {
        $todo = Todo::factory()->create(['status' => 'Todo']);

        $payload = [
            'title' => 'Aktualisiert',
            'category' => 'Work',
            'priority' => 'Low',
            'status' => 'Done',
        ];

        $response = $this->putJson(route('v1.todos.update', $todo->id), $payload);

        $response->assertOk()
            ->assertJsonPath('data.title', 'Aktualisiert')
            ->assertJsonPath('data.status', 'Done');
    }

    public function test_can_delete_todo(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->deleteJson(route('v1.todos.destroy', $todo->id));

        $response->assertNoContent();
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_requires_authentication(): void
    {
        $this->app['auth']->forgetGuards();

        $this->getJson(route('v1.todos.index'))->assertUnauthorized();
    }
}
