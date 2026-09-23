<?php

use App\Models\User;
use Domain\Models\JiraSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('jira api token is encrypted at rest and never serialized', function () {
    $setting = JiraSetting::query()->create([
        'user_id' => User::factory()->create()->id,
        'jira_url' => 'https://example.atlassian.net',
        'jira_email' => 'dev@example.com',
        'jira_api_token' => 'secret-token',
    ]);

    expect(DB::table('jira_settings')->value('jira_api_token'))->not->toBe('secret-token')
        ->and($setting->fresh()->jira_api_token)->toBe('secret-token')
        ->and($setting->toArray())->not->toHaveKey('jira_api_token');
});
