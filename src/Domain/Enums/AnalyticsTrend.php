<?php

declare(strict_types=1);

namespace Domain\Enums;

enum AnalyticsTrend: string
{
    case Up = 'up';
    case Down = 'down';
    case Stable = 'stable';
}
