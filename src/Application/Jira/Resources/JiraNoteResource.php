<?php

namespace Application\Jira\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JiraNoteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'user_id' => $this->user_id,
            'jira_issue_id' => $this->jira_issue_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Conditional relationships
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
            'jira_issue' => $this->whenLoaded('jiraIssue', function () {
                return [
                    'id' => $this->jiraIssue->id,
                    'jira_key' => $this->jiraIssue->jira_key,
                    'summary' => $this->jiraIssue->summary,
                ];
            }),
        ];
    }
}
