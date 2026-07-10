<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\AlertMessage;

class Alert extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'user_id',
        'subject',
        'description',
        'severity',
        'status',
        'admin_response',
        'triggered_at',
        'resolved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the device that owns the alert.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AlertMessage::class)->with('user')->orderBy('created_at');
    }

    public function getStatusBadgeAttribute(): string
    {
        $color = match ($this->status) {
            'viewed' => 'success',
            'pending' => 'warning',
            default => 'danger',
        };

        return sprintf('<span class="badge bg-%s">%s</span>', $color, ucfirst(str_replace('_', ' ', $this->status)));
    }
}
