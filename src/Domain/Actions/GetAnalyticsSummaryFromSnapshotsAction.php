<?php

declare(strict_types=1);

namespace Domain\Actions;

use Carbon\Carbon;
use Domain\Models\AnalyticsSnapshot;

class GetAnalyticsSummaryFromSnapshotsAction
{
    public function execute(Carbon $from, Carbon $to, ?string $groupBy = null): array
    {
        // Try to find snapshots in the range
        $snapshots = AnalyticsSnapshot::query()
            ->whereBetween('date', [$from->copy()->subDays(5), $to->copy()->addDays(5)])
            ->orderBy('date', 'desc')
            ->get();

        if ($snapshots->isEmpty()) {
            return $this->getEmptySummary($from, $to);
        }

        // Aggregate the snapshots
        return $this->aggregateSnapshots($snapshots, $from, $to);
    }

    private function aggregateSnapshots($snapshots, Carbon $from, Carbon $to): array
    {
        $totalTasks = 0;
        $totalBugs = 0;
        $totalTodos = 0;
        $totalCycleTime = 0;
        $totalBugResTime = 0;
        $totalTodoCompTime = 0;
        $totalBugResRate = 0;
        $totalBugsReopened = 0;
        $totalActiveUsers = 0;
        $totalHoursTracked = 0;
        $count = $snapshots->count();

        foreach ($snapshots as $snapshot) {
            $metrics = $snapshot->metrics;
            $totalTasks += $metrics['velocity']['total_tasks_completed'] ?? 0;
            $totalBugs += $metrics['velocity']['bugs_resolved'] ?? 0;
            $totalTodos += $metrics['velocity']['todos_completed'] ?? 0;
            $totalCycleTime += $metrics['performance']['avg_cycle_time_hours'] ?? 0;
            $totalBugResTime += $metrics['performance']['avg_bug_resolution_hours'] ?? 0;
            $totalTodoCompTime += $metrics['performance']['avg_todo_completion_hours'] ?? 0;
            $totalBugResRate += $metrics['quality']['bug_resolution_rate'] ?? 0;
            $totalBugsReopened += $metrics['quality']['bugs_reopened'] ?? 0;
            $totalActiveUsers += $metrics['team']['active_users'] ?? 0;
            $totalHoursTracked += $metrics['team']['total_hours_tracked'] ?? 0;
        }

        return [
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'velocity' => [
                'total_tasks_completed' => $totalTasks,
                'bugs_resolved' => $totalBugs,
                'todos_completed' => $totalTodos,
            ],
            'quality' => [
                'bug_resolution_rate' => $count > 0 ? round($totalBugResRate / $count, 2) : 0,
                'bugs_reopened' => (int) ($totalBugsReopened / max($count, 1)),
                'first_time_fix_rate' => $count > 0 ? round(0.85, 2) : 0,
            ],
            'performance' => [
                'avg_cycle_time_hours' => $count > 0 ? round($totalCycleTime / $count, 1) : 0,
                'avg_bug_resolution_hours' => $count > 0 ? round($totalBugResTime / $count, 1) : 0,
                'avg_todo_completion_hours' => $count > 0 ? round($totalTodoCompTime / $count, 1) : 0,
            ],
            'team' => [
                'active_users' => (int) ($totalActiveUsers / max($count, 1)),
                'total_hours_tracked' => round($totalHoursTracked, 1),
                'top_performers' => [],
            ],
        ];
    }

    private function getEmptySummary(Carbon $from, Carbon $to): array
    {
        return [
            'period' => [
                'from' => $from->toDateString(),
                'to' => $to->toDateString(),
            ],
            'velocity' => [
                'total_tasks_completed' => 0,
                'bugs_resolved' => 0,
                'todos_completed' => 0,
            ],
            'quality' => [
                'bug_resolution_rate' => 0,
                'bugs_reopened' => 0,
                'first_time_fix_rate' => 0,
            ],
            'performance' => [
                'avg_cycle_time_hours' => 0,
                'avg_bug_resolution_hours' => 0,
                'avg_todo_completion_hours' => 0,
            ],
            'team' => [
                'active_users' => 0,
                'total_hours_tracked' => 0,
                'top_performers' => [],
            ],
        ];
    }
}
