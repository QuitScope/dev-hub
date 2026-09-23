<?php

namespace Application\Jira\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class JiraSearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        return $query->where(function (Builder $query) use ($value) {
            $query->where('summary', 'like', "%{$value}%")
                ->orWhere('description', 'like', "%{$value}%")
                ->orWhere('jira_key', 'like', "%{$value}%");
        });
    }
}
