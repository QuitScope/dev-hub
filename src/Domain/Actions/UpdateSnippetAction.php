<?php

namespace Domain\Actions;

use Domain\Models\Snippet;

class UpdateSnippetAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(Snippet $snippet, array $data): Snippet
    {
        $snippet->update($data);

        return $snippet->refresh();
    }
}
