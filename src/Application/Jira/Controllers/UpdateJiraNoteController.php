<?php

namespace Application\Jira\Controllers;

use Application\Jira\Requests\UpdateJiraNoteRequest;
use Application\Jira\Resources\JiraNoteResource;
use Domain\Actions\Jira\UpdateJiraNoteAction;
use Domain\Models\JiraNote;
use Illuminate\Http\JsonResponse;

class UpdateJiraNoteController
{
    public function __invoke(UpdateJiraNoteRequest $request, JiraNote $jiraNote): JsonResponse
    {
        $this->authorize('update', $jiraNote);

        $note = UpdateJiraNoteAction::execute(
            note: $jiraNote,
            content: $request->validated('content')
        );

        return response()->json([
            'message' => 'Jira note updated successfully',
            'data' => new JiraNoteResource($note),
        ]);
    }
}
