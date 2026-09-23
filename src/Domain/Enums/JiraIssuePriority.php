<?php

namespace Domain\Enums;

enum JiraIssuePriority: string
{
    case HIGHEST = 'Highest';
    case HIGH = 'High';
    case MEDIUM = 'Medium';
    case LOW = 'Low';
    case LOWEST = 'Lowest';

    public function numericValue(): int
    {
        return match ($this) {
            self::HIGHEST => 5,
            self::HIGH => 4,
            self::MEDIUM => 3,
            self::LOW => 2,
            self::LOWEST => 1,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::HIGHEST => 'red',
            self::HIGH => 'orange',
            self::MEDIUM => 'yellow',
            self::LOW => 'blue',
            self::LOWEST => 'gray',
        };
    }
}
