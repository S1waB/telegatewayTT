<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Command extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'user_id',
        'payload',
        'status',
        'response',
        'sent_at',
        'response_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
        'response_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function getStatusBadgeAttribute(): string
    {
        $color = match ($this->status) {
            'pending' => 'warning',
            'sent' => 'info',
            'success' => 'success',
            'failed' => 'danger',
            default => 'secondary',
        };

        return sprintf('<span class="badge bg-%s">%s</span>', $color, ucfirst($this->status));
    }
}
