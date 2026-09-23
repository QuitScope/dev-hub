<?php

namespace Application\Jira\Controllers;

use Application\Jira\Requests\UpdateJiraNoteRequest;
use Application\Jira\Resources\JiraNoteResource;
use Domain\Models\JiraNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class UpdateJiraNoteController
{
    public function __invoke(UpdateJiraNoteRequest $request, JiraNote $jiraNote): JsonResponse
    {
        Gate::authorize('update', $jiraNote);

        $jiraNote->update(['note' => $request->validated('note')]);

        return response()->json([
            'message' => 'Jira note updated successfully',
            'data' => new JiraNoteResource($jiraNote),
        ]);
    }
}
