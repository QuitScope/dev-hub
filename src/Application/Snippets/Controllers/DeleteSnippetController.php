<?php

namespace Application\Snippets\Controllers;

use Domain\Actions\DeleteSnippetAction;
use Domain\Models\Snippet;
use Illuminate\Http\Response;

class DeleteSnippetController
{
    public function __construct(private DeleteSnippetAction $deleteSnippet) {}

    public function __invoke(Snippet $snippet): Response
    {
        ($this->deleteSnippet)($snippet);

        return response()->noContent();
    }
}
