<?php

declare(strict_types=1);

namespace Application\Bugs\Controllers;

use Application\Bugs\Requests\StoreBugRequest;
use Application\Bugs\Resources\BugResource;
use Domain\Actions\CreateBugAction;
use Illuminate\Http\JsonResponse;

class StoreBugController
{
    public function __construct(private CreateBugAction $createBug) {}

    public function __invoke(StoreBugRequest $request): JsonResponse
    {
        $bug = $this->createBug->execute($request->validated());

        return (new BugResource($bug))
            ->response()
            ->setStatusCode(201);
    }
}
