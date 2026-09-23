<?php

declare(strict_types=1);

namespace Domain\Actions;

use Domain\Enums\AnalyticsPeriod;
use Domain\Models\AnalyticsSnapshot;

class ForecastAnalyticsAction
{
    public function execute(string $metric, int $periods): array
    {
        $historicalData = AnalyticsSnapshot::query()
            ->where('period', AnalyticsPeriod::Monthly)
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get();

        if ($historicalData->count() < 3) {
            return [
                'data' => [
                    'metric' => $metric,
                    'historical' => [],
                    'forecast' => [],
                    'error' => 'Insufficient historical data for forecasting (minimum 3 months required)',
                ],
                'meta' => [
                    'model' => 'simple_linear_regression',
                    'generated_at' => now()->toISOString(),
                ],
            ];
        }

        $historical = $historicalData->reverse()->map(function ($snapshot) use ($metric) {
            return [
                'period' => $snapshot->date->format('Y-m'),
                'value' => $this->extractMetricValue($metric, $snapshot->metrics),
            ];
        })->values()->toArray();

        $forecast = $this->generateLinearForecast($historical, $periods);

        return [
            'data' => [
                'metric' => $metric,
                'historical' => $historical,
                'forecast' => $forecast,
            ],
            'meta' => [
                'model' => 'simple_linear_regression',
                'generated_at' => now()->toISOString(),
            ],
        ];
    }

    private function extractMetricValue(string $metric, array $metrics): float
    {
        return match ($metric) {
            'velocity' => (float) ($metrics['velocity']['total_tasks_completed'] ?? 0),
            'cycle_time' => (float) ($metrics['performance']['avg_cycle_time_hours'] ?? 0),
            'bug_resolution_rate' => (float) ($metrics['quality']['bug_resolution_rate'] ?? 0),
            default => 0,
        };
    }

    private function generateLinearForecast(array $historical, int $periods): array
    {
        $values = array_column($historical, 'value');
        $n = count($values);

        $sumX = array_sum(range(0, $n - 1));
        $sumY = array_sum($values);
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($values as $index => $value) {
            $sumXY += $index * $value;
            $sumX2 += $index * $index;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        $forecast = [];
        $lastDate = $historical[count($historical) - 1]['period'];
        $baseConfidence = 0.85;

        for ($i = 1; $i <= $periods; $i++) {
            $nextValue = $intercept + $slope * ($n + $i - 1);
            $nextDate = now()->parse($lastDate)->addMonths($i)->format('Y-m');
            $confidence = max(0.5, $baseConfidence - ($i * 0.08));

            $forecast[] = [
                'period' => $nextDate,
                'value' => max(0, round($nextValue, 1)),
                'confidence' => round($confidence, 2),
            ];
        }

        return $forecast;
    }
}
