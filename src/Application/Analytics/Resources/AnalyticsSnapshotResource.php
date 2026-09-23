<?php

declare(strict_types=1);

namespace Application\Analytics\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnalyticsSnapshotResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'date' => $this->date->toDateString(),
            'period' => $this->period->value,
            'metrics' => $this->metrics,
        ];
    }
}
