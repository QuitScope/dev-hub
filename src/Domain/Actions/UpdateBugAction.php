<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Models\Bug;

class UpdateBugAction
{
    public function execute(Bug $bug, array $data): Bug
    {
        $bug->update($data);

        return $bug->fresh();
    }
}
