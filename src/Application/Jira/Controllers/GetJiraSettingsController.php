<?php

namespace Application\Jira\Controllers;

use Application\Jira\Resources\JiraSettingResource;
use Domain\Models\JiraSetting;
use Illuminate\Http\JsonResponse;

class GetJiraSettingsController
{
    public function __invoke(): JsonResponse
    {
        $setting = JiraSetting::where('user_id', auth()->id())
            ->firstOrNew(['user_id' => auth()->id()]);

        return response()->json([
            'data' => new JiraSettingResource($setting),
        ]);
    }
}
