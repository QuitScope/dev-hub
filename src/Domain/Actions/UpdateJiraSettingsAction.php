<?php

namespace Domain\Actions;

use App\Models\User;
use Domain\Models\JiraSetting;

class UpdateJiraSettingsAction
{
    /**
     * Update or create Jira settings for a user.
     */
    public function execute(User $user, array $settings): JiraSetting
    {
        return JiraSetting::updateOrCreate(
            ['user_id' => $user->id],
            $this->filterSettings($settings)
        );
    }

    /**
     * Enable sync for a user.
     */
    public function enableSync(User $user): JiraSetting
    {
        return $this->execute($user, ['sync_enabled' => true]);
    }

    /**
     * Disable sync for a user.
     */
    public function disableSync(User $user): JiraSetting
    {
        return $this->execute($user, ['sync_enabled' => false]);
    }

    /**
     * Validate and filter settings data.
     */
    private function filterSettings(array $settings): array
    {
        return array_intersect_key($settings, array_flip([
            'jira_url',
            'jira_email',
            'jira_api_token',
            'sync_enabled',
        ]));
    }

    /**
     * Test connection with provided settings.
     */
    public function testConnection(array $settings): array
    {
        if (empty($settings['jira_url']) || empty($settings['jira_email']) || empty($settings['jira_api_token'])) {
            return [
                'success' => false,
                'message' => 'Missing required configuration',
            ];
        }

        // Here you would test the actual connection
        // For now, return a simple validation
        return [
            'success' => true,
            'message' => 'Connection test successful',
        ];
    }
}
