<?php

namespace Application\Jira\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJiraSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jira_url' => ['nullable', 'url', 'max:255'],
            'jira_email' => ['nullable', 'string', 'max:255'],
            'jira_api_token' => ['nullable', 'string', 'max:1000'],
            'sync_enabled' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'jira_url' => 'Die Jira URL muss eine gültige URL sein.',
            'jira_api_token.max' => 'Das Jira API Token ist zu lang.',
        ];
    }
}
