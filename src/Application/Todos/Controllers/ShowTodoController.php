<?php

namespace Application\Todos\Controllers;

use Application\Todos\Requests\ShowTodoRequest;
use Application\Todos\Resources\TodoResource;
use Domain\Models\Todo;
use Illuminate\Http\JsonResponse;

class ShowTodoController
{
    public function __invoke(ShowTodoRequest $request, Todo $todo): JsonResponse
    {
        return response()->json([
            'data' => new TodoResource($todo),
        ]);
    }
}
