<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Models\Bug;

class DeleteBugAction
{
    public function execute(Bug $bug): bool
    {
        return $bug->delete();
    }
}
