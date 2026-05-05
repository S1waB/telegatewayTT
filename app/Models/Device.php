<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Services\AIService;

class Device extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'serial_number',
        'device_type_id',
        'user_id',
        'status',
        'ip_address',
        'location',
        'avatar',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function commands(): HasMany
    {
        return $this->hasMany(Command::class);
    }

    public function data(): HasMany
    {
        return $this->hasMany(DeviceData::class, 'device_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('device_type_id', $typeId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return Storage::url($this->avatar);
        }

        $icon = $this->type ? $this->type->icon : 'router';
        // Placeholder for device avatar
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=0D4A8A&color=fff";
    }

    /**
     * AI Accessors
     */
    public function getAiFailureProbabilityAttribute(): int
    {
        return (new AIService())->predictFailureProbability($this);
    }

    public function getAiStatusAttribute(): string
    {
        return (new AIService())->classifyStatus($this);
    }

    public function getAiAdviceAttribute(): array
    {
        return (new AIService())->getAdvice($this);
    }
}
