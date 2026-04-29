<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',
        'subject',
        'description',
        'status',
        'admin_response',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AlertAttachment::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'not_viewed' => '<span class="badge bg-danger">Not Viewed</span>',
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'viewed' => '<span class="badge bg-success">Viewed</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
