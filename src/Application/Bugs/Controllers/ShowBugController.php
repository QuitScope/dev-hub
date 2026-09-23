<?php

declare(strict_types=1);

namespace Application\Bugs\Controllers;

use Application\Bugs\Resources\BugResource;
use Domain\Models\Bug;

class ShowBugController
{
    public function __invoke(Bug $bug): BugResource
    {
        return new BugResource($bug);
    }
}
