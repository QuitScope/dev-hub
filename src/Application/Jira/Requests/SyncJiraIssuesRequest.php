<?php

namespace Application\Jira\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncJiraIssuesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'force' => ['nullable', 'boolean'],
            'assignee' => ['nullable', 'string', 'max:255'],
            'project' => ['nullable', 'string', 'max:50'],
            'max_results' => ['nullable', 'integer', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'max_results.max' => 'Maximum 500 issues can be synced at once.',
        ];
    }
}
