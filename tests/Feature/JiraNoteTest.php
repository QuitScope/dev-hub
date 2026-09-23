<?php

use App\Models\User;
use Domain\Enums\JiraIssuePriority;
use Domain\Enums\JiraIssueStatus;
use Domain\Enums\JiraIssueType;
use Domain\Models\JiraIssue;
use Domain\Models\JiraNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->issue = JiraIssue::query()->create([
        'jira_id' => '10001',
        'jira_key' => 'DEV-1',
        'summary' => 'Test issue',
        'status' => JiraIssueStatus::TODO,
        'priority' => JiraIssuePriority::MEDIUM,
        'issue_type' => JiraIssueType::TASK,
        'url' => 'https://example.atlassian.net/browse/DEV-1',
    ]);

    Sanctum::actingAs($this->user);
});

test('a user can create, update and delete a note', function () {
    $noteId = $this->postJson(route('v1.jira.notes.store'), [
        'jira_issue_id' => $this->issue->id,
        'note' => 'First note',
    ])->assertCreated()->assertJsonPath('data.note', 'First note')->json('data.id');

    $this->putJson(route('v1.jira.notes.update', $noteId), ['note' => 'Edited'])
        ->assertOk()
        ->assertJsonPath('data.note', 'Edited');

    $this->deleteJson(route('v1.jira.notes.destroy', $noteId))->assertOk();

    expect(JiraNote::query()->count())->toBe(0);
});

test('notes of other users cannot be changed', function () {
    $note = JiraNote::query()->create([
        'user_id' => User::factory()->create()->id,
        'jira_issue_id' => $this->issue->id,
        'note' => 'Not yours',
    ]);

    $this->putJson(route('v1.jira.notes.update', $note), ['note' => 'Hacked'])->assertForbidden();
    $this->deleteJson(route('v1.jira.notes.destroy', $note))->assertForbidden();

    expect($note->fresh()->note)->toBe('Not yours');
});
