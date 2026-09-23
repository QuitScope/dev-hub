<?php

namespace Application\Snippets\Controllers;

use Application\Snippets\Queries\ListSnippetsQuery;
use Application\Snippets\Requests\ListSnippetsRequest;
use Application\Snippets\Resources\SnippetResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListSnippetsController
{
    public function __invoke(ListSnippetsRequest $request): AnonymousResourceCollection
    {
        $snippets = ListSnippetsQuery::build()
            ->paginate($request->input('limit', 20));

        return SnippetResource::collection($snippets);
    }
}
