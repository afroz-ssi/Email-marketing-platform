<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutlookAccount extends Model
{
    protected $fillable = [
        'email',
        'daily_limit',
        'sent_today',
        'status',
    ];

    protected $casts = [
        'daily_limit' => 'integer',
        'sent_today' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'outlook_email', 'email');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'queued');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'queued')
                    ->whereColumn('sent_today', '<', 'daily_limit');
    }

    // Methods
    public function getRemainingLimitAttribute()
    {
        return max(0, $this->daily_limit - $this->sent_today);
    }

    public function getUsagePercentageAttribute()
    {
        return $this->daily_limit > 0 ? round(($this->sent_today / $this->daily_limit) * 100, 2) : 0;
    }

    public function canSendEmail()
    {
        return $this->status === 'queued' && $this->sent_today < $this->daily_limit;
    }

    public function resetDailyCount()
    {
        $this->update([
            'sent_today' => 0,
            'status' => 'queued'
        ]);
    }
}
