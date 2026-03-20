<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'service',
        'message',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }
}
