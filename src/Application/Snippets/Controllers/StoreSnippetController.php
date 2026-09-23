<?php

namespace Application\Snippets\Controllers;

use Application\Snippets\Requests\StoreSnippetRequest;
use Application\Snippets\Resources\SnippetResource;
use Domain\Actions\CreateSnippetAction;
use Illuminate\Http\JsonResponse;

class StoreSnippetController
{
    public function __construct(private CreateSnippetAction $createSnippet) {}

    public function __invoke(StoreSnippetRequest $request): JsonResponse
    {
        $snippet = ($this->createSnippet)($request->validated());

        return response()->json([
            'data' => new SnippetResource($snippet),
        ], 201);
    }
}
