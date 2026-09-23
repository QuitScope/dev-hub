<?php

declare(strict_types=1);

namespace Application\Bugs\Controllers;

use Application\Bugs\Resources\BugResource;
use Domain\Models\Bug;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListBugsController
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $bugs = Bug::query()
            ->orderBy('reported_date', 'desc')
            ->paginate(20);

        return BugResource::collection($bugs);
    }
}
