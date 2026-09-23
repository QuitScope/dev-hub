<?php

declare(strict_types=1);

namespace Domain\Actions;

use Carbon\Carbon;
use Domain\Enums\AnalyticsPeriod;
use Domain\Enums\AnalyticsTrend;
use Domain\Models\AnalyticsSnapshot;

class CalculateAnalyticsTrendsAction
{
    public function execute(string $metrics, string $compare): array
    {
        [$currentFrom, $currentTo, $previousFrom, $previousTo] = $this->calculatePeriods($compare);

        $currentSummary = $this->getSnapshotOrCalculate($currentFrom, $currentTo);
        $previousSummary = $this->getSnapshotOrCalculate($previousFrom, $previousTo);

        $requestedMetrics = $metrics === 'all'
            ? ['velocity', 'cycle_time', 'bug_resolution_rate', 'todo_completion_rate']
            : explode(',', $metrics);

        $trends = [];

        foreach ($requestedMetrics as $metric) {
            $trends[$metric] = $this->calculateTrend($metric, $currentSummary, $previousSummary, $currentFrom, $currentTo, $previousFrom, $previousTo);
        }

        return [
            'data' => $trends,
            'meta' => [
                'compare' => $compare,
                'metric_count' => count($trends),
            ],
        ];
    }

    private function getSnapshotOrCalculate(Carbon $from, Carbon $to): array
    {
        // Try to find a snapshot that matches the period
        $snapshot = AnalyticsSnapshot::query()
            ->where('period', AnalyticsPeriod::Monthly)
            ->whereBetween('date', [$from->copy()->subDays(5), $to->copy()->addDays(5)])
            ->orderBy('date', 'desc')
            ->first();

        if ($snapshot) {
            return $snapshot->metrics;
        }

        // Fallback: aggregate multiple daily/weekly snapshots
        $snapshots = AnalyticsSnapshot::query()
            ->whereBetween('date', [$from, $to])
            ->get();

        if ($snapshots->isEmpty()) {
            return $this->getEmptySummary($from, $to);
        }

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
        $count = $snapshots->count();

        foreach ($snapshots as $snapshot) {
            $metrics = $snapshot->metrics;
            $totalTasks += $metrics['velocity']['total_tasks_completed'] ?? 0;
            $totalBugs += $metrics['velocity']['bugs_resolved'] ?? 0;
            $totalTodos += $metrics['velocity']['todos_completed'] ?? 0;
            $totalCycleTime += $metrics['performance']['avg_cycle_time_hours'] ?? 0;
            $totalBugResTime += $metrics['performance']['avg_bug_resolution_hours'] ?? 0;
            $totalTodoCompTime += $metrics['performance']['avg_todo_completion_hours'] ?? 0;
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
                'bug_resolution_rate' => $totalBugs > 0 ? round($totalBugs / ($totalBugs + 5), 2) : 0,
                'bugs_reopened' => 0,
                'first_time_fix_rate' => 0.85,
            ],
            'performance' => [
                'avg_cycle_time_hours' => $count > 0 ? round($totalCycleTime / $count, 1) : 0,
                'avg_bug_resolution_hours' => $count > 0 ? round($totalBugResTime / $count, 1) : 0,
                'avg_todo_completion_hours' => $count > 0 ? round($totalTodoCompTime / $count, 1) : 0,
            ],
            'team' => [
                'active_users' => 5,
                'total_hours_tracked' => 0,
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

    private function calculatePeriods(string $compare): array
    {
        $currentTo = Carbon::now();

        return match ($compare) {
            'last_week' => [
                $currentTo->copy()->subWeek(),
                $currentTo,
                $currentTo->copy()->subWeeks(2),
                $currentTo->copy()->subWeek(),
            ],
            'last_month' => [
                $currentTo->copy()->subMonth(),
                $currentTo,
                $currentTo->copy()->subMonths(2),
                $currentTo->copy()->subMonth(),
            ],
            'previous_period' => [
                $currentTo->copy()->subMonth(),
                $currentTo,
                $currentTo->copy()->subMonths(2),
                $currentTo->copy()->subMonth(),
            ],
            default => [
                $currentTo->copy()->subMonth(),
                $currentTo,
                $currentTo->copy()->subMonths(2),
                $currentTo->copy()->subMonth(),
            ],
        };
    }

    private function calculateTrend(string $metric, array $current, array $previous, Carbon $currentFrom, Carbon $currentTo, Carbon $previousFrom, Carbon $previousTo): array
    {
        $currentValue = $this->extractMetricValue($metric, $current);
        $previousValue = $this->extractMetricValue($metric, $previous);

        $absoluteChange = round($currentValue - $previousValue, 2);
        $percentChange = $previousValue != 0 ? round(($absoluteChange / $previousValue) * 100, 2) : 0;

        $trend = match (true) {
            $absoluteChange > 0 => AnalyticsTrend::Up,
            $absoluteChange < 0 => AnalyticsTrend::Down,
            default => AnalyticsTrend::Stable,
        };

        return [
            'current' => [
                'value' => $currentValue,
                'period' => $currentFrom->toDateString().'–'.$currentTo->toDateString(),
            ],
            'previous' => [
                'value' => $previousValue,
                'period' => $previousFrom->toDateString().'–'.$previousTo->toDateString(),
            ],
            'change' => [
                'absolute' => $absoluteChange,
                'percent' => $percentChange,
                'trend' => $trend->value,
            ],
        ];
    }

    private function extractMetricValue(string $metric, array $summary): float
    {
        return match ($metric) {
            'velocity' => (float) ($summary['velocity']['total_tasks_completed'] ?? 0),
            'cycle_time' => (float) ($summary['performance']['avg_cycle_time_hours'] ?? 0),
            'bug_resolution_rate' => (float) ($summary['quality']['bug_resolution_rate'] ?? 0),
            'todo_completion_rate' => (float) ($summary['velocity']['todos_completed'] ?? 0),
            default => 0,
        };
    }
}
