<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'message',
        'sent_count',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Methods
    public function getSuccessRateAttribute()
    {
        $totalSent = $this->emailLogs()->count();
        return $totalSent > 0 ? round(($this->sent_count / $totalSent) * 100, 2) : 0;
    }

    public function getUniqueRecipientsAttribute()
    {
        return $this->emailLogs()->distinct('lead_id')->count();
    }
}
