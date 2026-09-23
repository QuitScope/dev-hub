<?php

namespace Domain\Enums;

enum JiraIssueType: string
{
    case BUG = 'Bug';
    case STORY = 'Story';
    case TASK = 'Task';
    case EPIC = 'Epic';
    case SUBTASK = 'Sub-task';
    case IMPROVEMENT = 'Improvement';
    case NEW_FEATURE = 'New Feature';

    public function icon(): string
    {
        return match ($this) {
            self::BUG => '🐛',
            self::STORY => '📖',
            self::TASK => '📋',
            self::EPIC => '🎯',
            self::SUBTASK => '📝',
            self::IMPROVEMENT => '⚡',
            self::NEW_FEATURE => '✨',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BUG => 'red',
            self::STORY => 'green',
            self::TASK => 'blue',
            self::EPIC => 'purple',
            self::SUBTASK => 'gray',
            self::IMPROVEMENT => 'orange',
            self::NEW_FEATURE => 'cyan',
        };
    }
}
