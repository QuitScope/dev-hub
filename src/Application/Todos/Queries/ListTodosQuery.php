<?php

namespace Application\Todos\Queries;

use Application\Todos\Filters\TodoPriorityFilter;
use Application\Todos\Filters\TodoSearchFilter;
use Application\Todos\Filters\TodoStatusFilter;
use Domain\Models\Todo;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListTodosQuery
{
    public static function build(): QueryBuilder
    {
        return QueryBuilder::for(Todo::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new TodoSearchFilter),
                AllowedFilter::custom('status', new TodoStatusFilter),
                AllowedFilter::custom('priority', new TodoPriorityFilter),
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('updated_at'),
                AllowedSort::field('title'),
                AllowedSort::field('priority'),
                AllowedSort::field('status'),
            ])
            ->defaultSort('-created_at');
    }
}
