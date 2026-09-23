<?php

namespace Support\Jira;

use Carbon\Carbon;
use Domain\Enums\JiraIssuePriority;
use Domain\Enums\JiraIssueStatus;
use Domain\Enums\JiraIssueType;

class JiraIssueMapper
{
    /**
     * Map Jira API response to our domain model format.
     */
    public function mapFromJiraResponse(array $jiraIssue): array
    {
        $fields = $jiraIssue['fields'] ?? [];

        return [
            'jira_id' => $jiraIssue['id'],
            'jira_key' => $jiraIssue['key'],
            'summary' => $fields['summary'] ?? '',
            'description' => $this->cleanDescription($fields['description'] ?? ''),
            'status' => $this->mapStatus($fields['status']['name'] ?? 'Unknown'),
            'priority' => $this->mapPriority($fields['priority']['name'] ?? 'Medium'),
            'issue_type' => $this->mapIssueType($fields['issuetype']['name'] ?? 'Task'),
            'assignee' => $fields['assignee']['displayName'] ?? null,
            'jira_created_at' => $this->parseJiraDate($fields['created'] ?? null),
            'jira_updated_at' => $this->parseJiraDate($fields['updated'] ?? null),
            'url' => $this->buildIssueUrl($jiraIssue['key'], $jiraIssue['self'] ?? ''),
        ];
    }

    /**
     * Map Jira status to our enum.
     */
    private function mapStatus(string $jiraStatus): string
    {
        return match (strtolower($jiraStatus)) {
            'to do', 'open', 'new', 'backlog' => JiraIssueStatus::TODO->value,
            'in progress', 'in-progress', 'in development', 'selected for development' => JiraIssueStatus::IN_PROGRESS->value,
            'done', 'closed', 'resolved', 'complete' => JiraIssueStatus::DONE->value,
            'blocked', 'on hold', 'waiting' => JiraIssueStatus::BLOCKED->value,
            'cancelled', 'wont do', 'rejected' => JiraIssueStatus::CANCELLED->value,
            'in review', 'review', 'code review' => JiraIssueStatus::REVIEW->value,
            'testing', 'qa', 'ready for testing' => JiraIssueStatus::TESTING->value,
            default => JiraIssueStatus::TODO->value,
        };
    }

    /**
     * Map Jira priority to our enum.
     */
    private function mapPriority(string $jiraPriority): string
    {
        return match (strtolower($jiraPriority)) {
            'highest', 'critical', 'blocker' => JiraIssuePriority::HIGHEST->value,
            'high', 'major' => JiraIssuePriority::HIGH->value,
            'medium', 'normal' => JiraIssuePriority::MEDIUM->value,
            'low', 'minor' => JiraIssuePriority::LOW->value,
            'lowest', 'trivial' => JiraIssuePriority::LOWEST->value,
            default => JiraIssuePriority::MEDIUM->value,
        };
    }

    /**
     * Map Jira issue type to our enum.
     */
    private function mapIssueType(string $jiraType): string
    {
        return match (strtolower($jiraType)) {
            'bug', 'defect' => JiraIssueType::BUG->value,
            'story', 'user story' => JiraIssueType::STORY->value,
            'task' => JiraIssueType::TASK->value,
            'epic' => JiraIssueType::EPIC->value,
            'sub-task', 'subtask', 'sub task' => JiraIssueType::SUBTASK->value,
            'improvement', 'enhancement' => JiraIssueType::IMPROVEMENT->value,
            'new feature', 'feature' => JiraIssueType::NEW_FEATURE->value,
            default => JiraIssueType::TASK->value,
        };
    }

    /**
     * Clean and format Jira description.
     */
    private function cleanDescription(?string $description): ?string
    {
        if (empty($description)) {
            return null;
        }

        // If it's ADF (Atlassian Document Format), extract text
        if (is_string($description) && str_contains($description, '"type":"doc"')) {
            return $this->extractTextFromAdf($description);
        }

        // Clean HTML if present
        return strip_tags($description);
    }

    /**
     * Extract plain text from Atlassian Document Format.
     */
    private function extractTextFromAdf(string $adf): string
    {
        try {
            $decoded = json_decode($adf, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->extractTextFromAdfNode($decoded);
            }
        } catch (\Exception $e) {
            // Fallback to original string
        }

        return strip_tags($adf);
    }

    /**
     * Recursively extract text from ADF nodes.
     */
    private function extractTextFromAdfNode(array $node): string
    {
        $text = '';

        if (isset($node['text'])) {
            $text .= $node['text'];
        }

        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                $text .= $this->extractTextFromAdfNode($child);
                if (in_array($node['type'] ?? '', ['paragraph', 'heading'])) {
                    $text .= "\n";
                }
            }
        }

        return $text;
    }

    /**
     * Parse Jira date format to Carbon instance.
     */
    private function parseJiraDate(?string $jiraDate): ?Carbon
    {
        if (empty($jiraDate)) {
            return null;
        }

        try {
            return Carbon::parse($jiraDate);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build issue URL from Jira response.
     */
    private function buildIssueUrl(string $key, string $selfUrl): ?string
    {
        if (empty($selfUrl)) {
            return null;
        }

        // Extract base URL from self URL
        $baseUrl = preg_replace('#/rest/api/.*#', '', $selfUrl);

        return $baseUrl.'/browse/'.$key;
    }
}
