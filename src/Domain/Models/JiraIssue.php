<?php

namespace Domain\Models;

use Domain\Enums\JiraIssuePriority;
use Domain\Enums\JiraIssueStatus;
use Domain\Enums\JiraIssueType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JiraIssue extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $table = 'jira_issues';

    protected $fillable = [
        'id',
        'jira_id',
        'jira_key',
        'summary',
        'description',
        'status',
        'priority',
        'issue_type',
        'assignee',
        'jira_created_at',
        'jira_updated_at',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'status' => JiraIssueStatus::class,
            'priority' => JiraIssuePriority::class,
            'issue_type' => JiraIssueType::class,
            'jira_created_at' => 'datetime',
            'jira_updated_at' => 'datetime',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Get the notes for this Jira issue.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(JiraNote::class);
    }

    /**
     * Get snippets that reference this Jira issue.
     */
    public function snippets(): HasMany
    {
        return $this->hasMany(Snippet::class, 'jira_issue_id');
    }

    /**
     * Get todos that reference this Jira issue.
     */
    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class, 'jira_issue_id');
    }

    /**
     * Check if this issue is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    /**
     * Get the display URL for the issue.
     */
    public function getDisplayUrlAttribute(): string
    {
        return $this->url ?? '#';
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, JiraIssueStatus $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope to filter by priority.
     */
    public function scopeByPriority($query, JiraIssuePriority $priority): void
    {
        $query->where('priority', $priority);
    }

    /**
     * Scope to filter by assignee.
     */
    public function scopeByAssignee($query, string $assignee): void
    {
        $query->where('assignee', $assignee);
    }
}
