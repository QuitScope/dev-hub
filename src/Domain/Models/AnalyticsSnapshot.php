<?php

declare(strict_types=1);

namespace Domain\Models;

use Database\Factories\AnalyticsSnapshotFactory;
use Domain\Enums\AnalyticsPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnalyticsSnapshot extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $table = 'analytics_snapshots';

    protected $fillable = [
        'id',
        'date',
        'period',
        'metrics',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'period' => AnalyticsPeriod::class,
            'metrics' => 'array',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    protected static function newFactory(): AnalyticsSnapshotFactory
    {
        return AnalyticsSnapshotFactory::new();
    }
}
