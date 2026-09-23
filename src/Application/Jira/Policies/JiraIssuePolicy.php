<?php

namespace Application\Jira\Policies;

use App\Models\User;
use Domain\Models\JiraIssue;

class JiraIssuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JiraIssue $jiraIssue): bool
    {
        return true;
    }

    /**
     * Determine whether the user can sync issues.
     */
    public function sync(User $user): bool
    {
        // User needs to have Jira settings configured
        return $user->jiraSetting?->canSync() ?? false;
    }
}
