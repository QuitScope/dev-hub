<?php

declare(strict_types=1);

namespace Application\Bugs\Requests;

use Domain\Enums\BugPriority;
use Domain\Enums\BugStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBugRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['sometimes', 'string', Rule::enum(BugPriority::class)],
            'status' => ['sometimes', 'string', Rule::enum(BugStatus::class)],
            'reportedDate' => ['sometimes', 'date'],
            'processedDate' => ['nullable', 'date'],
            'resolvedDate' => ['nullable', 'date'],
            'reporter' => ['sometimes', 'string', 'max:255'],
            'assignee' => ['nullable', 'string', 'max:255'],
            'jiraIssue' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'priority.enum' => 'The priority must be one of: low, medium, high, critical',
            'status.enum' => 'The status must be one of: reported, in_progress, resolved, closed',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        $mapped = [];

        if (isset($validated['title'])) {
            $mapped['title'] = $validated['title'];
        }
        if (array_key_exists('description', $validated)) {
            $mapped['description'] = $validated['description'];
        }
        if (isset($validated['priority'])) {
            $mapped['priority'] = $validated['priority'];
        }
        if (isset($validated['status'])) {
            $mapped['status'] = $validated['status'];
        }
        if (isset($validated['reportedDate'])) {
            $mapped['reported_date'] = $validated['reportedDate'];
        }
        if (array_key_exists('processedDate', $validated)) {
            $mapped['processed_date'] = $validated['processedDate'];
        }
        if (array_key_exists('resolvedDate', $validated)) {
            $mapped['resolved_date'] = $validated['resolvedDate'];
        }
        if (isset($validated['reporter'])) {
            $mapped['reporter'] = $validated['reporter'];
        }
        if (array_key_exists('assignee', $validated)) {
            $mapped['assignee'] = $validated['assignee'];
        }
        if (array_key_exists('jiraIssue', $validated)) {
            $mapped['jira_issue'] = $validated['jiraIssue'];
        }
        if (array_key_exists('tags', $validated)) {
            $mapped['tags'] = $validated['tags'] ?? [];
        }

        return $mapped;
    }
}
