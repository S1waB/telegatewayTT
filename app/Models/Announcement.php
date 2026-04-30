<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'message',
        'target_type',
        'target_ids',
        'status',
        'category',
        'scheduled_at',
        'attachments',
        'sent_by'
    ];

    protected $casts = [
        'target_ids' => 'array',
        'scheduled_at' => 'datetime',
        'attachments' => 'array'
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
