<?php

namespace Application\Snippets\Queries;

use Application\Snippets\Filters\SnippetLanguageFilter;
use Application\Snippets\Filters\SnippetSearchFilter;
use Application\Snippets\Filters\SnippetTagsFilter;
use Domain\Models\Snippet;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListSnippetsQuery
{
    public static function build(): QueryBuilder
    {
        return QueryBuilder::for(Snippet::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new SnippetSearchFilter),
                AllowedFilter::custom('language', new SnippetLanguageFilter),
                AllowedFilter::custom('tags', new SnippetTagsFilter),
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('updated_at'),
                AllowedSort::field('title'),
                AllowedSort::field('language'),
            ])
            ->defaultSort('-created_at');
    }
}
