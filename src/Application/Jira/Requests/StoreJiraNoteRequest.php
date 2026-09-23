<?php

namespace Application\Jira\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJiraNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jira_issue_id' => ['required', 'uuid', 'exists:jira_issues,id'],
            'note' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'jira_issue_id.required' => 'Jira Issue ID ist erforderlich.',
            'jira_issue_id.exists' => 'Das angegebene Jira Issue existiert nicht.',
            'note.required' => 'Die Notiz darf nicht leer sein.',
            'note.max' => 'Die Notiz darf maximal 10.000 Zeichen lang sein.',
        ];
    }
}
