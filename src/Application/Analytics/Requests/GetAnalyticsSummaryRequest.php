<?php

declare(strict_types=1);

namespace Application\Analytics\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetAnalyticsSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'group_by' => ['nullable', 'string', 'in:day,week,month'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.required' => 'The from date is required',
            'from.date' => 'The from field must be a valid date',
            'to.required' => 'The to date is required',
            'to.date' => 'The to field must be a valid date',
            'to.after_or_equal' => 'The to date must be after or equal to the from date',
            'group_by.in' => 'The group_by field must be one of: day, week, month',
        ];
    }
}
