<?php

namespace Domain\Enums;

enum JiraIssueStatus: string
{
    case TODO = 'To Do';
    case IN_PROGRESS = 'In Progress';
    case DONE = 'Done';
    case BLOCKED = 'Blocked';
    case CANCELLED = 'Cancelled';
    case REVIEW = 'In Review';
    case TESTING = 'Testing';

    public function color(): string
    {
        return match ($this) {
            self::TODO => 'gray',
            self::IN_PROGRESS => 'blue',
            self::DONE => 'green',
            self::BLOCKED => 'red',
            self::CANCELLED => 'red',
            self::REVIEW => 'orange',
            self::TESTING => 'yellow',
        };
    }

    public function isCompleted(): bool
    {
        return in_array($this, [self::DONE, self::CANCELLED]);
    }
}
