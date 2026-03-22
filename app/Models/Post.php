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
        'source_file', 'editorial_comments',
    ];

    protected $casts = [
        'source_topics'      => 'array',
        'editorial_comments' => 'array',
        'published_at'       => 'datetime',
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

    public function addComment(string $text, string $type = 'correction'): void
    {
        $comments = $this->editorial_comments ?? [];
        $comments[] = [
            'id'         => Str::random(8),
            'text'       => $text,
            'type'       => $type, // correction, suggestion, approval
            'created_at' => now()->toISOString(),
        ];
        $this->update(['editorial_comments' => $comments]);
    }

    public function clearComments(): void
    {
        $this->update(['editorial_comments' => []]);
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

    public function scopeNeedsReview($query)
    {
        return $query->whereIn('status', ['pending', 'draft']);
    }
}
