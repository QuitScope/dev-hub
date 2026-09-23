<?php

namespace Domain\Exceptions;

use Exception;

class JiraApiException extends Exception
{
    public static function requestFailed(int $statusCode, string $responseBody): self
    {
        return new self(
            "Jira API request failed: {$statusCode} - {$responseBody}"
        );
    }

    public static function notConfigured(): self
    {
        return new self('Jira API client not properly configured');
    }
}
