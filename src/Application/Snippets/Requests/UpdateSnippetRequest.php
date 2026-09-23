<?php

namespace Application\Snippets\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSnippetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'code' => ['sometimes', 'string'],
            'language' => ['sometimes', 'string', 'max:64'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string'],
            'jira_issue' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
