<?php

namespace Application\Jira\Resources;

use Application\Snippets\Resources\SnippetResource;
use Application\Todos\Resources\TodoResource;
use Illuminate\Http\Resources\Json\JsonResource;

class JiraIssueResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'jira_id' => $this->jira_id,
            'jira_key' => $this->jira_key,
            'summary' => $this->summary,
            'description' => $this->description,
            'status' => [
                'value' => $this->status->value,
                'color' => $this->status->color(),
                'is_completed' => $this->status->isCompleted(),
            ],
            'priority' => [
                'value' => $this->priority->value,
                'numeric_value' => $this->priority->numericValue(),
                'color' => $this->priority->color(),
            ],
            'issue_type' => [
                'value' => $this->issue_type->value,
                'icon' => $this->issue_type->icon(),
                'color' => $this->issue_type->color(),
            ],
            'assignee' => $this->assignee,
            'url' => $this->url,
            'jira_created_at' => $this->jira_created_at?->toISOString(),
            'jira_updated_at' => $this->jira_updated_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Conditional relationships
            'notes' => $this->whenLoaded('notes', function () {
                return JiraNoteResource::collection($this->notes);
            }),
            'snippets' => $this->whenLoaded('snippets', function () {
                return SnippetResource::collection($this->snippets);
            }),
            'todos' => $this->whenLoaded('todos', function () {
                return TodoResource::collection($this->todos);
            }),

            // Counts
            'notes_count' => $this->whenCounted('notes'),
            'snippets_count' => $this->whenCounted('snippets'),
            'todos_count' => $this->whenCounted('todos'),
        ];
    }
}
