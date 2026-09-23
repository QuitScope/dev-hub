<?php

namespace Application\Jira\Queries;

use Application\Jira\Filters\JiraAssigneeFilter;
use Application\Jira\Filters\JiraHasNotesFilter;
use Application\Jira\Filters\JiraIssueTypeFilter;
use Application\Jira\Filters\JiraPriorityFilter;
use Application\Jira\Filters\JiraSearchFilter;
use Application\Jira\Filters\JiraStatusFilter;
use Domain\Models\JiraIssue;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ListJiraIssuesQuery
{
    public static function build(): QueryBuilder
    {
        return QueryBuilder::for(JiraIssue::class)
            ->allowedFilters([
                AllowedFilter::custom('search', new JiraSearchFilter),
                AllowedFilter::custom('status', new JiraStatusFilter),
                AllowedFilter::custom('priority', new JiraPriorityFilter),
                AllowedFilter::custom('issue_type', new JiraIssueTypeFilter),
                AllowedFilter::custom('assignee', new JiraAssigneeFilter),
                AllowedFilter::custom('has_notes', new JiraHasNotesFilter),
            ])
            ->allowedSorts([
                AllowedSort::field('created_at'),
                AllowedSort::field('updated_at'),
                AllowedSort::field('jira_created_at'),
                AllowedSort::field('jira_updated_at'),
                AllowedSort::field('summary'),
                AllowedSort::field('priority'),
                AllowedSort::field('status'),
                AllowedSort::field('assignee'),
            ])
            ->defaultSort('-jira_updated_at')
            ->with([
                'notes' => function ($query) {
                    $query->where('user_id', auth()->id());
                },
            ])
            ->withCount([
                'notes' => function ($query) {
                    $query->where('user_id', auth()->id());
                },
                'snippets',
                'todos',
            ]);
    }
}
