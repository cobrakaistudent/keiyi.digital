<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseGroup extends Model
{
    protected $fillable = ['name', 'instructor_id', 'course_id', 'notes', 'status'];

    public function instructor() { return $this->belongsTo(User::class, 'instructor_id'); }
    public function course() { return $this->belongsTo(Course::class); }
    public function members() { return $this->belongsToMany(User::class, 'course_group_members', 'group_id')
        ->withPivot('status')->withTimestamps(); }

    public function completedCount(): int
    {
        return $this->members()->wherePivot('status', 'completed')->count();
    }

    public function totalCount(): int
    {
        return $this->members()->count();
    }

    public function progressPercent(): int
    {
        $total = $this->totalCount();
        return $total > 0 ? (int) round(($this->completedCount() / $total) * 100) : 0;
    }
}
