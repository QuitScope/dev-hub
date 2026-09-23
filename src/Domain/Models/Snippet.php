<?php

declare(strict_types=1);

namespace Domain\Models;

use Database\Factories\SnippetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Snippet extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $table = 'snippets';

    protected $fillable = [
        'id',
        'title',
        'description',
        'code',
        'language',
        'tags',
        'jira_issue',
        'jira_issue_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
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
    protected static function newFactory(): SnippetFactory
    {
        return SnippetFactory::new();
    }

    public function jiraIssue(): BelongsTo
    {
        return $this->belongsTo(JiraIssue::class, 'jira_issue_id');
    }
}
