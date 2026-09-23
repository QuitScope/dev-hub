<?php

namespace Application\Snippets\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class SnippetSearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        return $query->where(function (Builder $query) use ($value) {
            $query->where('title', 'like', "%{$value}%")
                ->orWhere('description', 'like', "%{$value}%")
                ->orWhere('code', 'like', "%{$value}%");
        });
    }
}
