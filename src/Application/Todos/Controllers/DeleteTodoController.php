<?php

declare(strict_types=1);

namespace Application\Todos\Controllers;

use Domain\Models\Todo;
use Illuminate\Http\Response;

class DeleteTodoController
{
    public function __invoke(string $id): Response
    {
        $todo = Todo::query()->findOrFail($id);
        $todo->delete();

        return response()->noContent();
    }
}
