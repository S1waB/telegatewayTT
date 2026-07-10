<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'serial_number',
        'name',
        'category',
        'device_type_id',
        'status',
        'ip_address',
        'location',
        'user_id',
        'last_seen_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    /**
     * Get the metrics for the device.
     */
    public function metrics(): HasMany
    {
        return $this->hasMany(DeviceMetric::class, 'device_id', 'device_id');
    }

    /**
     * Get the commands for the device.
     */
    public function commands(): HasMany
    {
        return $this->hasMany(Command::class);
    }

    /**
     * Alias for metrics to support legacy data relationship.
     */
    public function data(): HasMany
    {
        return $this->hasMany(DeviceMetric::class, 'device_id', 'device_id');
    }

    /**
     * Get the alerts for the device.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Get the device type (legacy relationship).
     */
    public function type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }

    /**
     * Get the operator assigned to the device.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope for assigned devices.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for active devices.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * AI Accessors
     */
    public function getAvatarUrlAttribute(): string
    {
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=0D4A8A&color=fff";
    }

    public function getAiFailureProbabilityAttribute(): int
    {
        return (new \App\Services\AIService())->predictFailureProbability($this);
    }

    public function getAiStatusAttribute(): string
    {
        return (new \App\Services\AIService())->classifyStatus($this);
    }

    public function getAiAdviceAttribute(): array
    {
        return (new \App\Services\AIService())->getAdvice($this);
    }
}
