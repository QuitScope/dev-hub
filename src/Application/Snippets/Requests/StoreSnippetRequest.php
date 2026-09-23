<?php

namespace Application\Snippets\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSnippetRequest extends FormRequest
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
            'code' => ['required', 'string'],
            'language' => ['required', 'string', 'max:64'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
            'jira_issue' => ['nullable', 'string', 'max:255'],
        ];
    }
}
