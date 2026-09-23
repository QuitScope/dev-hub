<?php

namespace Application\Jira\Controllers;

use Application\Jira\Resources\JiraIssueResource;
use Domain\Models\JiraIssue;
use Illuminate\Http\JsonResponse;

class ShowJiraIssueController
{
    public function __invoke(JiraIssue $jiraIssue): JsonResponse
    {
        // Load relationships
        $jiraIssue->load(['notes' => function ($query) {
            $query->forUser(auth()->user());
        }, 'snippets', 'todos']);

        return response()->json([
            'data' => new JiraIssueResource($jiraIssue),
        ]);
    }
}
