<?php

namespace Application\Snippets\Controllers;

use Application\Snippets\Requests\UpdateSnippetRequest;
use Application\Snippets\Resources\SnippetResource;
use Domain\Actions\UpdateSnippetAction;
use Domain\Models\Snippet;
use Illuminate\Http\JsonResponse;

class UpdateSnippetController
{
    public function __construct(private UpdateSnippetAction $updateSnippet) {}

    public function __invoke(UpdateSnippetRequest $request, Snippet $snippet): JsonResponse
    {
        $snippet = ($this->updateSnippet)($snippet, $request->validated());

        return response()->json([
            'data' => new SnippetResource($snippet),
        ]);
    }
}
