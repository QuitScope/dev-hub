<?php

declare(strict_types=1);

namespace Application\Analytics\Controllers;

use Domain\Actions\ForecastAnalyticsAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetAnalyticsForecastController
{
    public function __construct(private ForecastAnalyticsAction $forecastAnalytics) {}

    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'metric' => ['required', 'string', 'in:velocity,cycle_time,bug_resolution_rate'],
            'periods' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $metric = $request->query('metric');
        $periods = (int) $request->query('periods', 3);

        $result = $this->forecastAnalytics->execute($metric, $periods);

        return response()->json($result);
    }
}
