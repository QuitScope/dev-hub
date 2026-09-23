<?php

declare(strict_types=1);

namespace Domain\Actions;

use Carbon\Carbon;
use Domain\Enums\AnalyticsPeriod;
use Domain\Models\AnalyticsSnapshot;

class GenerateAnalyticsSnapshotAction
{
    public function __construct(private CalculateAnalyticsSummaryAction $calculateSummary) {}

    public function execute(Carbon $from, Carbon $to, AnalyticsPeriod $period): AnalyticsSnapshot
    {
        $metrics = $this->calculateSummary->execute($from, $to);

        return AnalyticsSnapshot::create([
            'date' => $to->toDateString(),
            'period' => $period,
            'metrics' => $metrics,
        ]);
    }

    public function generateForPeriod(Carbon $date, AnalyticsPeriod $period): AnalyticsSnapshot
    {
        [$from, $to] = $this->getPeriodRange($date, $period);

        return $this->execute($from, $to, $period);
    }

    private function getPeriodRange(Carbon $date, AnalyticsPeriod $period): array
    {
        return match ($period) {
            AnalyticsPeriod::Daily => [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ],
            AnalyticsPeriod::Weekly => [
                $date->copy()->startOfWeek(),
                $date->copy()->endOfWeek(),
            ],
            AnalyticsPeriod::Monthly => [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
            ],
        };
    }
}
