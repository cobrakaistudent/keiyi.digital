<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'type',
        'content_html',
        'video_url',
        'video_outline',
        'quiz_data',
        'interactive_data',
        'sort_order',
        'is_published',
        'pass_threshold',
    ];

    protected $casts = [
        'quiz_data' => 'array',
        'interactive_data' => 'array',
        'is_published' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function completions()
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function isCompletedBy(User $user): bool
    {
        return $this->completions()->where('user_id', $user->id)->exists();
    }

    public function completionFor(User $user): ?LessonCompletion
    {
        return $this->completions()->where('user_id', $user->id)->first();
    }
}
