<?php

declare(strict_types=1);

namespace Application\Bugs\Controllers;

use Application\Bugs\Requests\UpdateBugRequest;
use Application\Bugs\Resources\BugResource;
use Domain\Actions\UpdateBugAction;
use Domain\Models\Bug;

class UpdateBugController
{
    public function __construct(private UpdateBugAction $updateBug) {}

    public function __invoke(UpdateBugRequest $request, Bug $bug): BugResource
    {
        $updatedBug = $this->updateBug->execute($bug, $request->validated());

        return new BugResource($updatedBug);
    }
}
