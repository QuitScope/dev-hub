<?php

namespace Application\Jira\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JiraSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'jira_url' => $this->jira_url,
            'jira_email' => $this->jira_email,
            'sync_enabled' => $this->sync_enabled,
            'last_sync_at' => $this->last_sync_at?->toISOString(),
            'is_configured' => $this->isConfigured(),
            'can_sync' => $this->canSync(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Security: Never expose API token
            'has_api_token' => ! empty($this->jira_api_token),
        ];
    }
}
