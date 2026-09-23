<?php

declare(strict_types=1);

namespace Application\Analytics\Controllers;

use Application\Analytics\Requests\RecalculateAnalyticsRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Domain\Actions\GenerateAnalyticsSnapshotAction;
use Domain\Enums\AnalyticsPeriod;
use Illuminate\Http\JsonResponse;

class RecalculateAnalyticsController
{
    public function __construct(private GenerateAnalyticsSnapshotAction $generateSnapshot) {}

    public function __invoke(RecalculateAnalyticsRequest $request): JsonResponse
    {
        $startTime = microtime(true);

        $from = Carbon::parse($request->validated('from'));
        $to = Carbon::parse($request->validated('to'));
        $period = AnalyticsPeriod::from($request->validated('period'));

        $snapshotsGenerated = 0;

        $interval = match ($period) {
            AnalyticsPeriod::Daily => CarbonPeriod::create($from, '1 day', $to),
            AnalyticsPeriod::Weekly => CarbonPeriod::create($from, '1 week', $to),
            AnalyticsPeriod::Monthly => CarbonPeriod::create($from, '1 month', $to),
        };

        foreach ($interval as $date) {
            $this->generateSnapshot->generateForPeriod($date, $period);
            $snapshotsGenerated++;
        }

        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000);

        return response()->json([
            'data' => [
                'status' => 'ok',
                'snapshots_generated' => $snapshotsGenerated,
                'trends_updated' => 0,
            ],
            'meta' => [
                'execution_time_ms' => $executionTime,
            ],
        ]);
    }
}
