<?php

namespace Application\Jira\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class JiraHasNotesFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        $hasNotes = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        if ($hasNotes) {
            return $query->whereHas('notes', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
        }

        return $query->whereDoesntHave('notes', function (Builder $query) {
            $query->where('user_id', auth()->id());
        });
    }
}
