<?php

declare(strict_types=1);

namespace Domain\Models;

use Database\Factories\BugFactory;
use Domain\Enums\BugPriority;
use Domain\Enums\BugStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bug extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $table = 'bugs';

    protected $fillable = [
        'id',
        'title',
        'description',
        'priority',
        'status',
        'reported_date',
        'processed_date',
        'resolved_date',
        'reporter',
        'assignee',
        'jira_issue',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'priority' => BugPriority::class,
            'status' => BugStatus::class,
            'reported_date' => 'datetime',
            'processed_date' => 'datetime',
            'resolved_date' => 'datetime',
            'tags' => 'array',
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

        static::updating(function ($model) {
            // Auto-set processedDate when status changes to in_progress
            if ($model->isDirty('status') && $model->status === BugStatus::InProgress && ! $model->processed_date) {
                $model->processed_date = now();
            }

            // Auto-set resolvedDate when status changes to resolved
            if ($model->isDirty('status') && $model->status === BugStatus::Resolved && ! $model->resolved_date) {
                $model->resolved_date = now();
            }
        });
    }

    protected static function newFactory(): BugFactory
    {
        return BugFactory::new();
    }
}
