<?php

namespace Support\Jira;

use Domain\Exceptions\JiraApiException;
use Illuminate\Support\Facades\Http;

class JiraApiClient
{
    private ?string $baseUrl = null;

    private ?string $username = null;

    private ?string $apiToken = null;

    /**
     * Configure the client with authentication details.
     */
    public function configure(string $baseUrl, string $username, string $apiToken): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->username = $username;
        $this->apiToken = $apiToken;
    }

    /**
     * Get issues from Jira API.
     */
    public function getIssues(array $params = []): array
    {
        $this->ensureConfigured();

        $defaultParams = [
            'jql' => $this->buildJql($params),
            'maxResults' => $params['maxResults'] ?? 50,
            'startAt' => $params['startAt'] ?? 0,
            'fields' => implode(',', [
                'key',
                'summary',
                'description',
                'status',
                'priority',
                'issuetype',
                'assignee',
                'created',
                'updated',
            ]),
        ];

        $response = $this->makeRequest('GET', '/rest/api/3/search', $defaultParams);

        return $response['issues'] ?? [];
    }

    /**
     * Get a single issue by key.
     */
    public function getIssue(string $issueKey): ?array
    {
        $this->ensureConfigured();

        $response = $this->makeRequest('GET', "/rest/api/3/issue/{$issueKey}");

        return $response['errorMessages'] ?? null ? null : $response;
    }

    /**
     * Test the connection to Jira.
     */
    public function testConnection(): array
    {
        $this->ensureConfigured();

        try {
            $response = $this->makeRequest('GET', '/rest/api/3/myself');

            return [
                'success' => true,
                'user' => $response['displayName'] ?? $this->username,
                'message' => 'Connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Make an authenticated HTTP request to Jira API.
     */
    private function makeRequest(string $method, string $endpoint, array $params = []): array
    {
        $response = Http::withBasicAuth($this->username, $this->apiToken)
            ->acceptJson()
            ->timeout(30)
            ->$method($this->baseUrl.$endpoint, $params);

        if ($response->failed()) {
            throw JiraApiException::requestFailed($response->status(), $response->body());
        }

        return $response->json();
    }

    /**
     * Build JQL query from parameters.
     */
    private function buildJql(array $params): string
    {
        $conditions = [];

        if (! empty($params['assignee'])) {
            $conditions[] = "assignee = '{$params['assignee']}'";
        }

        if (! empty($params['status'])) {
            $statuses = is_array($params['status']) ? $params['status'] : [$params['status']];
            $statusList = implode(',', array_map(fn ($s) => "'{$s}'", $statuses));
            $conditions[] = "status IN ({$statusList})";
        }

        if (! empty($params['priority'])) {
            $priorities = is_array($params['priority']) ? $params['priority'] : [$params['priority']];
            $priorityList = implode(',', array_map(fn ($p) => "'{$p}'", $priorities));
            $conditions[] = "priority IN ({$priorityList})";
        }

        if (! empty($params['project'])) {
            $conditions[] = "project = '{$params['project']}'";
        }

        if (! empty($params['search'])) {
            $search = addslashes($params['search']);
            $conditions[] = "(summary ~ '{$search}' OR description ~ '{$search}')";
        }

        // Default to recent issues if no specific conditions
        if (empty($conditions)) {
            $conditions[] = 'updated >= -30d';
        }

        return implode(' AND ', $conditions).' ORDER BY updated DESC';
    }

    /**
     * Ensure the client is properly configured.
     */
    private function ensureConfigured(): void
    {
        if (! $this->baseUrl || ! $this->username || ! $this->apiToken) {
            throw JiraApiException::notConfigured();
        }
    }
}
