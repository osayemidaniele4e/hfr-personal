<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiClient extends Model
{
    protected $table = 'api_clients';

    protected $fillable = [
        'name',
        'email',
        'organisation',
        'api_key',
        'api_key_hash',
        'rate_limit',
        'is_active',
        'last_used_at',
        'expires_at',
        'description',
        'use_case',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'created_by',
    ];

    protected $hidden = [
        'api_key_hash',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'rate_limit' => 'integer',
    ];

    // ── Status constants ─────────────────────────────────────────
    const STATUS_PENDING  = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Relationship: reviewed by user.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Generate a new API key pair.
     * Returns the plain-text key (show once) and sets the hash.
     */
    public static function generateApiKey(): string
    {
        return 'hfr_' . Str::random(48);
    }

    /**
     * Hash an API key for storage.
     */
    public static function hashApiKey(string $plainKey): string
    {
        return hash('sha256', $plainKey);
    }

    /**
     * Check if the API key has expired.
     */
    public function hasExpired(): bool
    {
        if (is_null($this->expires_at)) {
            return false;
        }
        return $this->expires_at->isPast();
    }

    /**
     * Check if the client is usable (active and not expired).
     */
    public function isUsable(): bool
    {
        return $this->is_active && !$this->hasExpired();
    }

    /**
     * Touch the last_used_at timestamp.
     */
    public function touchLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Relationship: request logs.
     */
    public function requestLogs()
    {
        return $this->hasMany(ApiRequestLog::class, 'api_client_id');
    }

    /**
     * Relationship: created by user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
