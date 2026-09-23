<?php

use App\Models\User;
use Domain\Models\Snippet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class, WithFaker::class);

beforeEach(function () {
    $this->withoutExceptionHandling();

    $user = User::factory()->create(['email' => 'admin@admin.com']);

    Sanctum::actingAs($user, [], 'web');
});

test('it gets snippets list', function () {
    $snippets = Snippet::factory()
        ->count(5)
        ->create();

    $response = $this->get(route('v1.snippets.index'));

    $response->assertOk()->assertSee($snippets[0]->title);
});

test('it stores the snippet', function () {
    $data = Snippet::factory()
        ->make()
        ->toArray();

    $response = $this->postJson(route('v1.snippets.store'), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $this->assertDatabaseHas('snippets', Arr::except($data, 'tags'));

    $response->assertStatus(201)->assertJsonFragment(['title' => $data['title'], 'tags' => $data['tags']]);
});

test('it updates the snippet', function () {
    $snippet = Snippet::factory()->create();

    $data = [
        'title' => fake()->sentence(10),
        'description' => fake()->sentence(15),
        'code' => fake()
            ->unique()
            ->regexify('[A-Z]{3}[0-9]{3}'),
        'language' => fake()->languageCode(),
        'tags' => [],
        'jira_issue' => fake()->text(255),
    ];

    $response = $this->putJson(route('v1.snippets.update', $snippet), $data);

    unset($data['created_at']);
    unset($data['updated_at']);

    $data['id'] = $snippet->id;

    $this->assertDatabaseHas('snippets', Arr::except($data, 'tags'));

    $response->assertStatus(200)->assertJsonFragment(['title' => $data['title'], 'tags' => []]);
});

test('it deletes the snippet', function () {
    $snippet = Snippet::factory()->create();

    $response = $this->deleteJson(route('v1.snippets.destroy', $snippet));

    $this->assertModelMissing($snippet);

    $response->assertNoContent();
});
