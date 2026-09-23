<?php

declare(strict_types=1);

namespace Application\Analytics\Controllers;

use Application\Analytics\Requests\GetAnalyticsSummaryRequest;
use Carbon\Carbon;
use Domain\Actions\GetAnalyticsSummaryFromSnapshotsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class GetAnalyticsSummaryController
{
    public function __construct(private GetAnalyticsSummaryFromSnapshotsAction $calculateSummary) {}

    public function __invoke(GetAnalyticsSummaryRequest $request): JsonResponse
    {
        $from = Carbon::parse($request->validated('from'));
        $to = Carbon::parse($request->validated('to'));
        $groupBy = $request->validated('group_by');

        $cacheKey = "analytics:summary:{$from->toDateString()}:{$to->toDateString()}:{$groupBy}";

        $data = Cache::remember($cacheKey, now()->addHour(), function () use ($from, $to, $groupBy) {
            return $this->calculateSummary->execute($from, $to, $groupBy);
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'cache' => Cache::has($cacheKey) ? 'hit' : 'miss',
                'generated_at' => now()->toISOString(),
            ],
        ]);
    }
}
