<?php

namespace Application\Jira\Controllers;

use Application\Jira\Requests\StoreJiraNoteRequest;
use Application\Jira\Resources\JiraNoteResource;
use Domain\Actions\CreateJiraNoteAction;
use Domain\Models\JiraIssue;
use Illuminate\Http\JsonResponse;

class StoreJiraNoteController
{
    public function __invoke(StoreJiraNoteRequest $request): JsonResponse
    {
        $jiraIssue = JiraIssue::findOrFail($request->validated('jira_issue_id'));

        $action = new CreateJiraNoteAction;

        $note = $action->execute(
            $request->user(),
            $jiraIssue,
            $request->validated('content')
        );

        return response()->json([
            'message' => 'Jira note created successfully',
            'data' => new JiraNoteResource($note),
        ], 201);
    }
}
