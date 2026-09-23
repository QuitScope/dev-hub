<?php

declare(strict_types=1);

namespace Application\Analytics\Requests;

use Domain\Enums\AnalyticsPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecalculateAnalyticsRequest extends FormRequest
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
            'period' => ['required', 'string', Rule::enum(AnalyticsPeriod::class)],
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
            'period.required' => 'The period field is required',
            'period.enum' => 'The period field must be one of: daily, weekly, monthly',
        ];
    }
}
