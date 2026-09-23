<?php

namespace Application\Jira\Controllers;

use Domain\Actions\Jira\DeleteJiraNoteAction;
use Domain\Models\JiraNote;
use Illuminate\Http\JsonResponse;

class DeleteJiraNoteController
{
    public function __invoke(JiraNote $jiraNote): JsonResponse
    {
        $this->authorize('delete', $jiraNote);

        DeleteJiraNoteAction::execute($jiraNote);

        return response()->json([
            'message' => 'Jira note deleted successfully',
        ]);
    }
}
