<?php

declare(strict_types=1);

namespace Application\Analytics\Controllers;

use Domain\Actions\CalculateAnalyticsTrendsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAnalyticsTrendsController
{
    public function __construct(private CalculateAnalyticsTrendsAction $calculateTrends) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'metrics' => ['nullable', 'string'],
            'compare' => ['nullable', 'string', 'in:last_week,last_month,previous_period'],
        ]);

        $metrics = $request->query('metrics', 'all');
        $compare = $request->query('compare', 'last_month');

        $result = $this->calculateTrends->execute($metrics, $compare);

        return response()->json($result);
    }
}
