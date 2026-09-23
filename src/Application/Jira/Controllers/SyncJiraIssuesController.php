<?php

namespace Application\Jira\Controllers;

use Application\Jira\Requests\SyncJiraIssuesRequest;
use Domain\Actions\SyncJiraIssuesAction;
use Domain\Models\JiraSetting;
use Illuminate\Http\JsonResponse;

class SyncJiraIssuesController
{
    public function __invoke(SyncJiraIssuesRequest $request): JsonResponse
    {
        $user = auth()->user();

        $setting = JiraSetting::where('user_id', $user->id)->first();

        if (! $setting || ! $setting->canSync()) {
            return response()->json([
                'success' => false,
                'message' => 'Jira sync is not configured for this user.',
            ], 400);
        }

        $syncAction = new SyncJiraIssuesAction;
        $result = $syncAction->execute($setting);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
