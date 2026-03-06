<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoutSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'type',
        'is_active',
        'relevance_score',
        'last_crawled_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'relevance_score' => 'integer',
        'last_crawled_at' => 'datetime',
    ];
}
