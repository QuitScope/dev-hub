<?php

declare(strict_types=1);

namespace Application\Bugs\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BugResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority->value,
            'status' => $this->status->value,
            'reportedDate' => $this->reported_date?->toISOString(),
            'processedDate' => $this->processed_date?->toISOString(),
            'resolvedDate' => $this->resolved_date?->toISOString(),
            'reporter' => $this->reporter,
            'assignee' => $this->assignee,
            'jiraIssue' => $this->jira_issue,
            'tags' => $this->tags ?? [],
        ];
    }
}
