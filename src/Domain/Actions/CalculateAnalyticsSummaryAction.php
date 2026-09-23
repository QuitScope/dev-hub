<?php

declare(strict_types=1);

namespace Domain\Actions;

use Carbon\Carbon;
use Domain\Models\JiraIssue;
use Domain\Models\Todo;
use Illuminate\Support\Facades\DB;

class CalculateAnalyticsSummaryAction
{
    public function execute(Carbon $from, Carbon $to, ?string $groupBy = null): array
    {
        $completedTodos = Todo::query()
            ->where('status', 'Done')
            ->whereBetween('updated_at', [$from, $to])
            ->count();

        $resolvedBugs = JiraIssue::query()
            ->where('issue_type', 'Bug')
            ->whereIn('status', ['Done', 'Resolved', 'Closed'])
            ->whereBetween('jira_updated_at', [$from, $to])
            ->count();

        $totalBugs = JiraIssue::query()
            ->where('issue_type', 'Bug')
            ->whereBetween('jira_created_at', [$from->copy()->subMonths(3), $to])
            ->count();

        $bugsReopened = JiraIssue::query()
            ->where('issue_type', 'Bug')
            ->whereIn('status', ['Reopened', 'In Progress'])
            ->whereBetween('jira_updated_at', [$from, $to])
            ->count();

        $avgCycleTime = $this->calculateAverageCycleTime($from, $to);
        $avgBugResolutionTime = $this->calculateAverageBugResolutionTime($from, $to);
        $avgTodoCompletionTime = $this->calculateAverageTodoCompletionTime($from, $to);

        $activeUsers = DB::table('todos')
            ->whereBetween('updated_at', [$from, $to])
            ->distinct('id')
            ->count();

        return [
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'velocity' => [
                'total_tasks_completed' => $completedTodos + $resolvedBugs,
                'bugs_resolved' => $resolvedBugs,
                'todos_completed' => $completedTodos,
            ],
            'quality' => [
                'bug_resolution_rate' => $totalBugs > 0 ? round($resolvedBugs / $totalBugs, 2) : 0,
                'bugs_reopened' => $bugsReopened,
                'first_time_fix_rate' => $totalBugs > 0 ? round(($resolvedBugs - $bugsReopened) / $totalBugs, 2) : 0,
            ],
            'performance' => [
                'avg_cycle_time_hours' => $avgCycleTime,
                'avg_bug_resolution_hours' => $avgBugResolutionTime,
                'avg_todo_completion_hours' => $avgTodoCompletionTime,
            ],
            'team' => [
                'active_users' => $activeUsers,
                'total_hours_tracked' => 0, // Placeholder for future time tracking
                'top_performers' => [],
            ],
        ];
    }

    private function calculateAverageCycleTime(Carbon $from, Carbon $to): float
    {
        $completedTasks = Todo::query()
            ->where('status', 'Done')
            ->whereBetween('updated_at', [$from, $to])
            ->get(['created_at', 'updated_at']);

        if ($completedTasks->isEmpty()) {
            return 0;
        }

        $totalHours = $completedTasks->sum(function ($task) {
            return $task->created_at->diffInHours($task->updated_at);
        });

        return round($totalHours / $completedTasks->count(), 1);
    }

    private function calculateAverageBugResolutionTime(Carbon $from, Carbon $to): float
    {
        $resolvedBugs = JiraIssue::query()
            ->where('issue_type', 'Bug')
            ->whereIn('status', ['Done', 'Resolved', 'Closed'])
            ->whereBetween('jira_updated_at', [$from, $to])
            ->get(['jira_created_at', 'jira_updated_at']);

        if ($resolvedBugs->isEmpty()) {
            return 0;
        }

        $totalHours = $resolvedBugs->sum(function ($bug) {
            return $bug->jira_created_at->diffInHours($bug->jira_updated_at);
        });

        return round($totalHours / $resolvedBugs->count(), 1);
    }

    private function calculateAverageTodoCompletionTime(Carbon $from, Carbon $to): float
    {
        $completedTodos = Todo::query()
            ->where('status', 'Done')
            ->whereBetween('updated_at', [$from, $to])
            ->get(['created_at', 'updated_at']);

        if ($completedTodos->isEmpty()) {
            return 0;
        }

        $totalHours = $completedTodos->sum(function ($todo) {
            return $todo->created_at->diffInHours($todo->updated_at);
        });

        return round($totalHours / $completedTodos->count(), 1);
    }
}
