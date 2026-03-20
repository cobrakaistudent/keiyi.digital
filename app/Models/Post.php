<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'category',
        'status', 'source_topics', 'dominant_subreddit',
        'word_count', 'rejection_reason', 'published_at',
    ];

    protected $casts = [
        'source_topics' => 'array',
        'published_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            $post->word_count = str_word_count(strip_tags($post->content ?? ''));
        });

        static::updating(function (Post $post) {
            $post->word_count = str_word_count(strip_tags($post->content ?? ''));
        });
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved', 'rejection_reason' => null]);
    }

    public function publish(): void
    {
        $this->update(['status' => 'published', 'published_at' => now()]);
    }

    public function reject(string $reason): void
    {
        $this->update(['status' => 'rejected', 'rejection_reason' => $reason]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->orderByDesc('published_at');
    }
}
