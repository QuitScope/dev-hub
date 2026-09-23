<?php

namespace Domain\Models;

use App\Models\User;
use Database\Factories\JiraNoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JiraNote extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $table = 'jira_notes';

    protected $fillable = [
        'id',
        'user_id',
        'jira_issue_id',
        'note',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Get the user that owns the note.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the Jira issue this note belongs to.
     */
    public function jiraIssue(): BelongsTo
    {
        return $this->belongsTo(JiraIssue::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): JiraNoteFactory
    {
        return JiraNoteFactory::new();
    }

    /**
     * Scope to filter notes by user.
     */
    public function scopeForUser($query, User $user): void
    {
        $query->where('user_id', $user->id);
    }
}
