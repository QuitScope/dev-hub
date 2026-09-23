<?php

namespace Domain\Actions;

use Domain\Models\Snippet;

class DeleteSnippetAction
{
    public function __invoke(Snippet $snippet): void
    {
        $snippet->delete();
    }
}
