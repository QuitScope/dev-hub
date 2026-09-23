<?php

namespace Application\Jira\Controllers;

use Application\Jira\Requests\UpdateJiraSettingsRequest;
use Application\Jira\Resources\JiraSettingResource;
use Domain\Actions\UpdateJiraSettingsAction;
use Illuminate\Http\JsonResponse;

class UpdateJiraSettingsController
{
    public function __invoke(UpdateJiraSettingsRequest $request): JsonResponse
    {
        $updateSettingsAction = new UpdateJiraSettingsAction;

        $setting = $updateSettingsAction->execute(
            auth()->user(),
            $request->validated()
        );

        return response()->json([
            'data' => new JiraSettingResource($setting),
            'message' => 'Settings updated successfully',
        ]);
    }
}
