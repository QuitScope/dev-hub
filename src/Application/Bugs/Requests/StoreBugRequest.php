<?php

declare(strict_types=1);

namespace Application\Bugs\Requests;

use Domain\Enums\BugPriority;
use Domain\Enums\BugStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBugRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'string', Rule::enum(BugPriority::class)],
            'status' => ['required', 'string', Rule::enum(BugStatus::class)],
            'reportedDate' => ['required', 'date'],
            'reporter' => ['required', 'string', 'max:255'],
            'assignee' => ['nullable', 'string', 'max:255'],
            'jiraIssue' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required',
            'priority.required' => 'The priority field is required',
            'priority.enum' => 'The priority must be one of: low, medium, high, critical',
            'status.required' => 'The status field is required',
            'status.enum' => 'The status must be one of: reported, in_progress, resolved, closed',
            'reportedDate.required' => 'The reported date is required',
            'reporter.required' => 'The reporter field is required',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'reported_date' => $validated['reportedDate'],
            'reporter' => $validated['reporter'],
            'assignee' => $validated['assignee'] ?? null,
            'jira_issue' => $validated['jiraIssue'] ?? null,
            'tags' => $validated['tags'] ?? [],
        ];
    }
}
