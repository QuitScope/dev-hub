<?php

namespace Application\Snippets\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class SnippetTagsFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): Builder
    {
        // Handle array of tags
        if (is_array($value)) {
            return $query->where(function (Builder $query) use ($value) {
                foreach ($value as $tag) {
                    $query->orWhereJsonContains('tags', $tag);
                }
            });
        }

        // Handle single tag
        return $query->whereJsonContains('tags', $value);
    }
}
