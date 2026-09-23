<?php

namespace Application\Jira\Requests;

use Domain\Enums\JiraIssuePriority;
use Domain\Enums\JiraIssueStatus;
use Domain\Enums\JiraIssueType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListJiraIssuesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Spatie Query Builder filter parameters
            'filter.search' => ['nullable', 'string', 'max:255'],
            'filter.status' => ['nullable', Rule::enum(JiraIssueStatus::class)],
            'filter.priority' => ['nullable', Rule::enum(JiraIssuePriority::class)],
            'filter.issue_type' => ['nullable', Rule::enum(JiraIssueType::class)],
            'filter.assignee' => ['nullable', 'string', 'max:255'],
            'filter.has_notes' => ['nullable', 'boolean'],

            // Spatie Query Builder sort parameter
            'sort' => ['nullable', 'string'],

            // Pagination
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
