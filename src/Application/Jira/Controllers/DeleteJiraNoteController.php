<?php

namespace Application\Jira\Controllers;

use Domain\Actions\DeleteJiraNoteAction;
use Domain\Models\JiraNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DeleteJiraNoteController
{
    public function __invoke(Request $request, JiraNote $jiraNote): JsonResponse
    {
        Gate::authorize('delete', $jiraNote);

        (new DeleteJiraNoteAction)->execute($request->user(), $jiraNote);

        return response()->json([
            'message' => 'Jira note deleted successfully',
        ]);
    }
}
