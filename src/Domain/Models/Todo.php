<?php

declare(strict_types=1);

namespace Domain\Models;

use Database\Factories\TodoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Todo extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $table = 'todos';

    protected $fillable = [
        'id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'jira_issue',
        'jira_issue_id',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): TodoFactory
    {
        return TodoFactory::new();
    }

    public function jiraIssue(): BelongsTo
    {
        return $this->belongsTo(JiraIssue::class, 'jira_issue_id');
    }
}
