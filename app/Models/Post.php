<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    private const ALLOWED_TAGS = '<p><h1><h2><h3><h4><ul><ol><li><strong><em><a><br><blockquote><code><pre><img><table><thead><tbody><tr><th><td>';

    public static function sanitizeHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Strip dangerous tags, keep structural HTML
        $clean = strip_tags($html, self::ALLOWED_TAGS);

        // Remove on* event attributes (onclick, onerror, etc.)
        $clean = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $clean);
        $clean = preg_replace('/\s+on\w+\s*=\s*\S+/i', '', $clean);

        // Remove javascript: and data: URLs from href/src
        $clean = preg_replace('/href\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', 'href="#"', $clean);
        $clean = preg_replace('/src\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', 'src=""', $clean);
        $clean = preg_replace('/href\s*=\s*["\']?\s*data:[^"\'>\s]*/i', 'href="#"', $clean);
        $clean = preg_replace('/src\s*=\s*["\']?\s*data:(?!image\/)[^"\'>\s]*/i', 'src=""', $clean);

        return $clean;
    }

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'category',
        'status', 'source_topics', 'dominant_subreddit',
        'word_count', 'rejection_reason', 'published_at',
        'source_file', 'editorial_comments',
    ];

    protected $casts = [
        'source_topics' => 'array',
        'editorial_comments' => 'array',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Post $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            $post->content = self::sanitizeHtml($post->content);
            $post->word_count = str_word_count(strip_tags($post->content ?? ''));
        });

        static::updating(function (Post $post) {
            if ($post->isDirty('content')) {
                $post->content = self::sanitizeHtml($post->content);
            }
            $post->word_count = str_word_count(strip_tags($post->content ?? ''));
        });
    }

    public function addComment(string $text, string $type = 'correction'): void
    {
        $comments = $this->editorial_comments ?? [];
        $comments[] = [
            'id' => Str::random(8),
            'text' => $text,
            'type' => $type, // correction, suggestion, approval
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
