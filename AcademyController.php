<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AcademyController extends Controller
{
    /**
     * Dashboard principal de la Academia
     */
    public function dashboard()
    {
        $courses = [
            [
                'title' => 'IA Origins',
                'slug' => 'ia-origins',
                'level' => 'Inicial',
                'description' => 'De zero a Power User: Historia, modelos y prompts de élite.',
                'image' => 'https://images.unsplash.com/photo-1504221507732-5246c045949b?auto=format&fit=crop&q=80&w=2532',
                'lesson_count' => 7
            ],
            [
                'title' => 'Notion Mastery',
                'slug' => 'notion-mastery',
                'level' => 'Intermedio',
                'description' => 'Domina Wikis, Proyectos y Agentes de IA en tu nuevo Segundo Cerebro.',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/4/45/Notion_app_logo.png', // LOGO DEFINITIVO APROBADO POR EL USUARIO
                'lesson_count' => 7
            ],
            [
                'title' => 'Marketing Elite',
                'slug' => 'marketing-elite',
                'level' => 'Elite',
                'description' => 'Sistemas de venta automatizados con IA, ManyChat y Claude 3.5.',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=2426',
                'lesson_count' => 7
            ],
            [
                'title' => 'Viral Contenido',
                'slug' => 'contenido-viral',
                'level' => 'Intermedio',
                'description' => 'Producción masiva de Reels y TikTok con IA a alta velocidad.',
                'image' => 'https://images.unsplash.com/photo-1611162616305-c69b3fa7fbe0?auto=format&fit=crop&q=80&w=2574',
                'lesson_count' => 4
            ],
            [
                'title' => '3D World',
                'slug' => '3d-world',
                'level' => 'Avanzado',
                'description' => 'Prototipado e invención física con IA e impresión 3D.',
                'image' => 'https://images.unsplash.com/photo-1633356122544-f134324a6cee?auto=format&fit=crop&q=80&w=2670',
                'lesson_count' => 4
            ],
            [
                'title' => 'Productividad Pro',
                'slug' => 'productividad-pro',
                'level' => 'Inicial',
                'description' => 'Domina tu computadora y ahorra horas cada día con IA.',
                'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=2672',
                'lesson_count' => 4
            ]
        ];

        return view('academy.dashboard', compact('courses'));
    }

    /**
     * Ver una lección específica con soporte para Guía y Script
     */
    public function showLesson($courseSlug, $lessonNumber, $type = 'guia')
    {
        // Mapeo dinámico basado en la estructura de archivos
        $folders = [
            'ia-origins' => 'ia_origins',
            'notion-mastery' => 'notion_mastery',
            'marketing-elite' => 'marketing_elite',
            'contenido-viral' => 'contenido_viral',
            'productividad-pro' => 'productividad_pro',
            '3d-world' => '3d_world'
        ];

        if (!isset($folders[$courseSlug])) abort(404);

        $folder = $folders[$courseSlug];
        $pattern = base_path("academy_content/{$folder}/Leccion_{$lessonNumber}_*.md");
        $files = glob($pattern);

        if (empty($files)) abort(404);

        // Identificar los dos tipos de archivos para esta lección
        $guiaFile = collect($files)->first(fn($f) => str_contains($f, 'Guia_Estudio'));
        $scriptFile = collect($files)->first(fn($f) => str_contains($f, 'Script_Video'));

        // Si no existen los archivos específicos, buscar el base
        if (!$guiaFile && !$scriptFile) {
            $guiaFile = $files[0];
        }

        $targetFile = ($type === 'script' && $scriptFile) ? $scriptFile : $guiaFile;
        
        if (!File::exists($targetFile)) abort(404);

        $content = File::get($targetFile);
        
        // Obtener el título de la lección del nombre del archivo
        $filename = basename($targetFile);
        $lessonTitle = Str::title(str_replace(['Leccion_', '_Guia_Estudio', '_Script_Video', '.md', '_'], ['Lección ', ' ', ' ', '', ' '], $filename));

        // Lista de todas las lecciones del curso para la barra lateral
        $allLessonsPattern = base_path("academy_content/{$folder}/Leccion_*_Guia_Estudio_*.md");
        $allLessonFiles = glob($allLessonsPattern);
        
        if (empty($allLessonFiles)) {
            $allLessonFiles = glob(base_path("academy_content/{$folder}/Leccion_*.md"));
        }

        $courseLessons = collect($allLessonFiles)->map(function($file) use ($courseSlug) {
            $fname = basename($file);
            preg_match('/Leccion_(\d+)_/', $fname, $matches);
            $num = $matches[1] ?? 1;
            return [
                'number' => $num,
                'title' => Str::title(str_replace(['Leccion_', '_Guia_Estudio', '.md', '_'], ['', '', '', ' '], $fname)),
                'url' => route('academy.lesson', [$courseSlug, $num])
            ];
        })->unique('number')->sortBy('number')->values();

        return view('academy.player', [
            'courseTitle' => Str::title(str_replace('-', ' ', $courseSlug)),
            'courseSlug' => $courseSlug,
            'currentLessonNumber' => $lessonNumber,
            'lessonTitle' => $lessonTitle,
            'courseLessons' => $courseLessons,
            'content' => $content,
            'type' => $type,
            'hasScript' => !!$scriptFile,
            'hasGuia' => !!$guiaFile
        ]);
    }

    /**
     * Descargar un recurso de la academia de forma segura
     */
    public function downloadResource($filename)
    {
        $filePath = base_path("academy_resources/{$filename}");

        if (!File::exists($filePath)) {
            abort(404, 'El recurso solicitado no existe.');
        }

        return response()->download($filePath);
    }

    /**
     * Gestión de Alumnos (Solo para Admin)
     */
    public function manageStudents()
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $students = \App\Models\User::where('role', 'student')->latest()->get();
        return view('academy.admin_students', compact('students'));
    }

    /**
     * Aprobar a un alumno (Solo para Admin)
     */
    public function approveStudent($id)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $user = \App\Models\User::findOrFail($id);
        $user->update(['is_approved' => true]);

        return back()->with('status', "El alumno {$user->name} ha sido aprobado con éxito.");
    }
}
