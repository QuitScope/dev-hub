<?php

declare(strict_types=1);

namespace Application\Todos\Controllers;

use Application\Todos\Requests\UpdateTodoRequest;
use Application\Todos\Resources\TodoResource;
use Domain\Models\Todo;
use Illuminate\Http\JsonResponse;

class UpdateTodoController
{
    public function __invoke(UpdateTodoRequest $request, string $id): JsonResponse
    {
        $todo = Todo::query()->findOrFail($id);
        $todo->update($request->validated());

        return response()->json([
            'data' => new TodoResource($todo),
        ]);
    }
}
