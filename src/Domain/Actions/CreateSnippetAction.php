<?php

namespace Domain\Actions;

use Domain\Models\Snippet;

class CreateSnippetAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(array $data): Snippet
    {
        return Snippet::query()->create($data);
    }
}
