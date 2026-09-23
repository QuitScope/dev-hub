<?php

namespace Application\Todos\Controllers;

use Application\Todos\Queries\ListTodosQuery;
use Application\Todos\Requests\ListTodosRequest;
use Application\Todos\Resources\TodoResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListTodosController
{
    public function __invoke(ListTodosRequest $request): AnonymousResourceCollection
    {
        $todos = ListTodosQuery::build()
            ->paginate($request->input('limit', 20));

        return TodoResource::collection($todos);
    }
}
