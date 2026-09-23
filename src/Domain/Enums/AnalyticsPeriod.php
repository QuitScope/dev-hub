<?php

declare(strict_types=1);

namespace Domain\Enums;

enum AnalyticsPeriod: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}
