<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
        'approval_status',
        'role',
        'is_3d_client',
        '3d_client_approved_at',
        'student_limit',
        'profesor_id',
        'company_name',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_3d_client' => 'boolean',
            '3d_client_approved_at' => 'datetime',
            'student_limit' => 'integer',
        ];
    }

    // ─── Role helpers ───

    public function isAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function canEnrollMoreStudents(): bool
    {
        return $this->isTeacher()
            && $this->student_limit
            && $this->students()->count() < $this->student_limit;
    }

    public function remainingStudentSlots(): int
    {
        if (! $this->isTeacher() || ! $this->student_limit) {
            return 0;
        }

        return max(0, $this->student_limit - $this->students()->count());
    }

    public function fullName(): string
    {
        $name = $this->name;
        if ($this->apellido_paterno) {
            $name .= ' '.$this->apellido_paterno;
        }
        if ($this->apellido_materno) {
            $name .= ' '.$this->apellido_materno;
        }

        return $name;
    }

    // ─── Filament ───

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'super-admin';
    }

    // ─── Relationships ───

    public function profesor()
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'profesor_id');
    }

    public function courseGroups()
    {
        return $this->hasMany(CourseGroup::class, 'instructor_id');
    }

    public function lessonCompletions()
    {
        return $this->hasMany(LessonCompletion::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
