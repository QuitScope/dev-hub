<?php

namespace Application\Todos\Controllers;

use Application\Todos\Requests\StoreTodoRequest;
use Application\Todos\Resources\TodoResource;
use Domain\Models\Todo;

class StoreTodoController
{
    public function __invoke(StoreTodoRequest $request): TodoResource
    {
        $todo = Todo::query()->create($request->validated());

        return TodoResource::make($todo);
    }
}
