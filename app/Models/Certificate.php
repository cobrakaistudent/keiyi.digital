<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Certificate extends Model
{
    protected $fillable = [
        'user_id', 'course_id', 'code', 'student_name',
        'course_title', 'score', 'issued_at', 'issued_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function course() { return $this->belongsTo(Course::class); }
    public function issuer() { return $this->belongsTo(User::class, 'issued_by'); }

    /**
     * Genera código alfanumérico único de 16 caracteres
     * Formato: KEIYI-XXXX-XXXX-XXXX (letras mayúsculas + números)
     */
    public static function generateCode(): string
    {
        do {
            $code = 'KY-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /**
     * Emite certificado para un usuario que completó un curso
     */
    public static function issue(User $user, Course $course, ?int $score = null, ?int $issuedBy = null): self
    {
        return self::create([
            'user_id'      => $user->id,
            'course_id'    => $course->id,
            'code'         => self::generateCode(),
            'student_name' => trim($user->name . ' ' . ($user->apellido_paterno ?? '') . ' ' . ($user->apellido_materno ?? '')),
            'course_title' => $course->title,
            'score'        => $score,
            'issued_at'    => now(),
            'issued_by'    => $issuedBy,
        ]);
    }

    /**
     * Verifica un código — retorna el certificado o null
     */
    public static function verify(string $code): ?self
    {
        return self::where('code', strtoupper(trim($code)))->first();
    }
}
