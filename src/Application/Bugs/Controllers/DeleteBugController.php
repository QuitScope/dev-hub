<?php

declare(strict_types=1);

namespace Application\Bugs\Controllers;

use Domain\Actions\DeleteBugAction;
use Domain\Models\Bug;
use Illuminate\Http\JsonResponse;

class DeleteBugController
{
    public function __construct(private DeleteBugAction $deleteBug) {}

    public function __invoke(Bug $bug): JsonResponse
    {
        $this->deleteBug->execute($bug);

        return response()->json([
            'message' => 'Bug successfully deleted',
        ]);
    }
}
