<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketResearchRequest extends Model
{
    protected $fillable = [
        'title',
        'purpose',
        'target_market',
        'priority',
        'status',
        'findings',
        'sources',
        'requested_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
