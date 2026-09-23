<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Models\Bug;

class CreateBugAction
{
    public function execute(array $data): Bug
    {
        return Bug::create($data);
    }
}
