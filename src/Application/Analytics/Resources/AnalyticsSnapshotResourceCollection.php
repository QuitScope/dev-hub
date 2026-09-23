<?php

declare(strict_types=1);

namespace Application\Analytics\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnalyticsSnapshotResourceCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'period' => $request->query('period'),
                'count' => $this->collection->count(),
                'cached' => false,
            ],
        ];
    }
}
