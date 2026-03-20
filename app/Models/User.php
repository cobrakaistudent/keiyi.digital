<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'password'               => 'hashed',
            'is_3d_client'           => 'boolean',
            '3d_client_approved_at'  => 'datetime',
        ];
    }

    /**
     * Restringe el acceso al panel Filament a usuarios con rol super-admin.
     * Método oficial de Filament v3 (interfaz FilamentUser).
     * Filament lo llama automáticamente antes de permitir entrada al panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'super-admin';
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
