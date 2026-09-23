<?php

namespace Domain\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class JiraSetting extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'id';

    protected $table = 'jira_settings';

    protected $hidden = [
        'jira_api_token',
    ];

    protected $fillable = [
        'id',
        'user_id',
        'jira_url',
        'jira_email',
        'jira_api_token',
        'sync_enabled',
        'last_sync_at',
    ];

    protected function casts(): array
    {
        return [
            'sync_enabled' => 'boolean',
            'last_sync_at' => 'datetime',
            'jira_api_token' => 'encrypted',
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
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    // protected static function newFactory(): JiraSettingFactory
    // {
    //     return JiraSettingFactory::new();
    // }

    /**
     * Check if Jira sync is properly configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->jira_url)
            && ! empty($this->jira_email)
            && ! empty($this->jira_api_token);
    }

    /**
     * Check if sync is enabled and configured.
     */
    public function canSync(): bool
    {
        return $this->sync_enabled && $this->isConfigured();
    }

    /**
     * Get the full Jira API base URL.
     */
    public function getApiBaseUrlAttribute(): string
    {
        return rtrim($this->jira_url, '/').'/rest/api/3';
    }

    /**
     * Scope to filter by sync-enabled users.
     */
    public function scopeSyncEnabled($query): void
    {
        $query->where('sync_enabled', true)
            ->whereNotNull('jira_url')
            ->whereNotNull('jira_email')
            ->whereNotNull('jira_api_token');
    }
}
