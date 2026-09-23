<?php

namespace Application\Jira\Policies;

use App\Models\User;
use Domain\Models\JiraNote;

class JiraNotePolicy
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
    public function view(User $user, JiraNote $jiraNote): bool
    {
        return $user->id === $jiraNote->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JiraNote $jiraNote): bool
    {
        return $user->id === $jiraNote->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JiraNote $jiraNote): bool
    {
        return $user->id === $jiraNote->user_id;
    }
}
