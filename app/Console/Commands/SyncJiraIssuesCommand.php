<?php

namespace App\Console\Commands;

use Domain\Actions\SyncJiraIssuesAction;
use Domain\Models\JiraSetting;
use Illuminate\Console\Command;

class SyncJiraIssuesCommand extends Command
{
    protected $signature = 'jira:sync {--user= : Sync issues for a specific user ID} {--all : Sync issues for all users with Jira settings}';

    protected $description = 'Sync Jira issues from the Jira API';

    public function handle(): int
    {
        $userId = $this->option('user');
        $all = $this->option('all');

        if (! $userId && ! $all) {
            $this->error('Please specify either --user=ID or --all option');

            return self::FAILURE;
        }

        $query = JiraSetting::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $settings = $query->get();

        if ($settings->isEmpty()) {
            $this->info('No Jira settings found');

            return self::SUCCESS;
        }

        $this->info("Found {$settings->count()} Jira configuration(s) to sync");

        $syncAction = new SyncJiraIssuesAction;

        foreach ($settings as $setting) {
            $this->info("Processing Jira sync for user {$setting->user_id}...");

            try {
                $result = $syncAction->execute($setting);

                $this->comment("Synced {$result['synced']} issues for user {$setting->user_id}");
            } catch (\Exception $e) {
                $this->error("Failed to sync Jira issues for user {$setting->user_id}: {$e->getMessage()}");
            }
        }

        $this->comment('Jira sync completed for all configurations');

        return self::SUCCESS;
    }
}
