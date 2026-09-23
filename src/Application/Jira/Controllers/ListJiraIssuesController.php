<?php

namespace Application\Jira\Controllers;

use Application\Jira\Queries\ListJiraIssuesQuery;
use Application\Jira\Requests\ListJiraIssuesRequest;
use Application\Jira\Resources\JiraIssueResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListJiraIssuesController
{
    public function __invoke(ListJiraIssuesRequest $request): AnonymousResourceCollection
    {
        $issues = ListJiraIssuesQuery::build()
            ->paginate($request->input('limit', 20));

        return JiraIssueResource::collection($issues);
    }
}
