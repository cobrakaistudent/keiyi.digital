<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'scout_metadata',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'scout_metadata' => 'array',
            'published_at' => 'datetime',
        ];
    }
}
