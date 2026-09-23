<?php

namespace Domain\Actions;

use App\Models\User;
use Domain\Models\JiraIssue;
use Domain\Models\JiraNote;

class CreateJiraNoteAction
{
    /**
     * Create a new Jira note.
     */
    public function execute(User $user, JiraIssue $jiraIssue, string $note): JiraNote
    {
        return JiraNote::create([
            'user_id' => $user->id,
            'jira_issue_id' => $jiraIssue->id,
            'note' => $note,
        ]);
    }

    /**
     * Create a note by Jira issue key.
     */
    public function executeByJiraKey(User $user, string $jiraKey, string $note): ?JiraNote
    {
        $jiraIssue = JiraIssue::where('jira_key', $jiraKey)->first();

        if (! $jiraIssue) {
            return null;
        }

        return $this->execute($user, $jiraIssue, $note);
    }

    /**
     * Update an existing note or create a new one.
     */
    public function updateOrCreate(User $user, JiraIssue $jiraIssue, string $note): JiraNote
    {
        return JiraNote::updateOrCreate(
            [
                'user_id' => $user->id,
                'jira_issue_id' => $jiraIssue->id,
            ],
            ['note' => $note]
        );
    }
}
