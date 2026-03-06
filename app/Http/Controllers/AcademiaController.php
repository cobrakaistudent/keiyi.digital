<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnrollmentConfirmation;

class AcademiaController extends Controller
{
    // Catálogo estático de cursos — se migra a DB cuando el contenido esté listo
    const COURSES = [
        'taller-0' => [
            'title'     => 'Taller 0: IA Origins & Motor Agentico',
            'emoji'     => '🤖',
            'desc'      => 'Desmitifica la IA: Tokens, Modelos de Razonamiento, MCP y Arquitectura Agentica. 3 dias intensivos.',
            'tag'       => 'Pre-requisito',
            'available' => false,
        ],
        'taller-1' => [
            'title'     => 'Taller 1: El Mapa de la IA (Ecosistema)',
            'emoji'     => '🗺️',
            'desc'      => 'Navega el ecosistema completo de herramientas de IA para marketing y agencias digitales en 2026.',
            'tag'       => 'Proximo',
            'available' => false,
        ],
        'taller-2' => [
            'title'     => 'Taller 2: Prompt Engineering Masterclass',
            'emoji'     => '⚡',
            'desc'      => 'De prompts basicos a ingenieria de contexto avanzada. Chain-of-Thought, RAG y control de salida.',
            'tag'       => 'Proximo',
            'available' => false,
        ],
        'marketing-elite' => [
            'title'     => 'Marketing Elite 2026',
            'emoji'     => '🚀',
            'desc'      => 'GEO, Performance, LTV/CAC, automatizacion con IA y casos reales Latam.',
            'tag'       => 'Proximo',
            'available' => false,
        ],
    ];

    public function dashboard()
    {
        $user        = Auth::user();
        $enrollments = DB::table('enrollments')->where('user_id', $user->id)->get()->keyBy('course_id');

        return view('academia.dashboard', [
            'user'        => $user,
            'enrollments' => $enrollments,
            'courses'     => self::COURSES,
        ]);
    }

    public function enroll(Request $request, string $courseId)
    {
        $user = Auth::user();

        if (! array_key_exists($courseId, self::COURSES)) {
            return back()->with('error', 'Curso no encontrado.');
        }

        // Ya inscrito
        $exists = DB::table('enrollments')
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            return back()->with('already_enrolled', self::COURSES[$courseId]['title']);
        }

        DB::table('enrollments')->insert([
            'user_id'          => $user->id,
            'course_id'        => $courseId,
            'progress_percent' => 0,
            'enrolled_at'      => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Email de confirmacion (silencioso si mail no esta configurado)
        try {
            Mail::to($user->email)->send(
                new EnrollmentConfirmation($user->name, self::COURSES[$courseId]['title'], $courseId)
            );
        } catch (\Throwable $e) {
            // Mail no configurado en este entorno — la inscripcion sigue siendo valida
        }

        return back()->with('enrolled', self::COURSES[$courseId]['title']);
    }
}
