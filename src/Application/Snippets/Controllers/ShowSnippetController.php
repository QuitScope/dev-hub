<?php

namespace Application\Snippets\Controllers;

use Application\Snippets\Requests\ShowSnippetRequest;
use Application\Snippets\Resources\SnippetResource;
use Domain\Models\Snippet;
use Illuminate\Http\JsonResponse;

class ShowSnippetController
{
    public function __invoke(ShowSnippetRequest $request, Snippet $snippet): JsonResponse
    {
        return response()->json([
            'data' => new SnippetResource($snippet),
        ]);
    }
}
