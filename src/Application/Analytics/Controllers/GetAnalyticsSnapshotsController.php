<?php

declare(strict_types=1);

namespace Application\Analytics\Controllers;

use Application\Analytics\Resources\AnalyticsSnapshotResource;
use Application\Analytics\Resources\AnalyticsSnapshotResourceCollection;
use Domain\Enums\AnalyticsPeriod;
use Domain\Models\AnalyticsSnapshot;
use Illuminate\Http\Request;

class GetAnalyticsSnapshotsController
{
    public function __invoke(Request $request): AnalyticsSnapshotResourceCollection
    {
        $request->validate([
            'period' => ['nullable', 'string', 'in:daily,weekly,monthly'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        $query = AnalyticsSnapshot::query();

        if ($request->has('period')) {
            $query->where('period', AnalyticsPeriod::from($request->query('period')));
        }

        if ($request->has('from')) {
            $query->where('date', '>=', $request->query('from'));
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->query('to'));
        }

        $limit = $request->query('limit', 30);
        $snapshots = $query->orderBy('date', 'desc')->limit($limit)->get();

        return new AnalyticsSnapshotResourceCollection(
            AnalyticsSnapshotResource::collection($snapshots)
        );
    }
}
