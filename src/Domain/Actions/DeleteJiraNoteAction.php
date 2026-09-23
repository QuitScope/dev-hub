<?php

namespace Domain\Actions;

use App\Models\User;
use Domain\Models\JiraNote;

class DeleteJiraNoteAction
{
    /**
     * Delete a Jira note if it belongs to the user.
     */
    public function execute(User $user, JiraNote $note): bool
    {
        if ($note->user_id !== $user->id) {
            return false;
        }

        return $note->delete();
    }

    /**
     * Delete all notes for a user and Jira issue.
     */
    public function deleteAllForUserAndIssue(User $user, string $jiraIssueId): int
    {
        return JiraNote::where('user_id', $user->id)
            ->where('jira_issue_id', $jiraIssueId)
            ->delete();
    }

    /**
     * Delete all notes for a user.
     */
    public function deleteAllForUser(User $user): int
    {
        return JiraNote::where('user_id', $user->id)->delete();
    }
}
