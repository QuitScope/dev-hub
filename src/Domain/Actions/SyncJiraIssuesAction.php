<?php

namespace Domain\Actions;

use Domain\Models\JiraIssue;
use Domain\Models\JiraSetting;
use Support\Jira\JiraApiClient;
use Support\Jira\JiraIssueMapper;

class SyncJiraIssuesAction
{
    /**
     * Sync Jira issues for a specific user.
     */
    public function execute(JiraSetting $setting): array
    {
        if (! $setting->canSync()) {
            return [
                'success' => false,
                'message' => 'Jira sync not properly configured',
                'synced' => 0,
            ];
        }

        try {
            $jiraClient = new JiraApiClient;
            $mapper = new JiraIssueMapper;

            // Configure client with user settings
            $jiraClient->configure(
                $setting->jira_url,
                $setting->jira_email,
                $setting->jira_api_token
            );

            // Fetch issues from Jira API
            $jiraIssues = $jiraClient->getIssues([
                'assignee' => $setting->jira_email,
                'maxResults' => 100,
            ]);

            $syncedCount = 0;

            foreach ($jiraIssues as $jiraIssue) {
                $mappedData = $mapper->mapFromJiraResponse($jiraIssue);

                JiraIssue::updateOrCreate(
                    ['jira_key' => $mappedData['jira_key']],
                    $mappedData
                );

                $syncedCount++;
            }

            // Update last sync time
            $setting->update(['last_sync_at' => now()]);

            return [
                'success' => true,
                'message' => "Successfully synced {$syncedCount} issues",
                'synced' => $syncedCount,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Sync failed: '.$e->getMessage(),
                'synced' => 0,
            ];
        }
    }

    /**
     * Sync issues for all enabled users.
     */
    public function syncAll(): array
    {
        $settings = JiraSetting::syncEnabled()->get();
        $results = [];

        foreach ($settings as $setting) {
            $result = $this->execute($setting);
            $result['user_id'] = $setting->user_id;
            $results[] = $result;
        }

        return $results;
    }
}
