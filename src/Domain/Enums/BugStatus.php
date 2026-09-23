<?php

declare(strict_types=1);

namespace Domain\Enums;

enum BugStatus: string
{
    case Reported = 'reported';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';
}
