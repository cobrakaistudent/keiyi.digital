<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentConfirmation;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AcademiaController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $enrollments = DB::table('enrollments')->where('user_id', $user->id)->get()->keyBy('course_id');

        $courses = Course::published()->orderBy('sort_order')->get();

        // Legacy courses not yet in DB — show as coming soon
        $legacyCourses = Course::where('is_published', false)->orderBy('sort_order')->get();

        return view('academia.dashboard', [
            'user' => $user,
            'enrollments' => $enrollments,
            'courses' => $courses,
            'legacyCourses' => $legacyCourses,
        ]);
    }

    public function enroll(Request $request, string $courseId)
    {
        $user = Auth::user();

        // Check if course exists in DB
        $course = Course::where('slug', $courseId)->first();
        if (! $course) {
            return back()->with('error', 'Curso no encontrado.');
        }

        // Ya inscrito
        $exists = DB::table('enrollments')
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->exists();

        if ($exists) {
            return back()->with('already_enrolled', $course->title);
        }

        DB::table('enrollments')->insert([
            'user_id' => $user->id,
            'course_id' => $courseId,
            'progress_percent' => 0,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Email de confirmacion (silencioso si mail no esta configurado)
        try {
            Mail::to($user->email)->send(
                new EnrollmentConfirmation($user->name, $course->title, $courseId)
            );
        } catch (\Throwable $e) {
            \Log::warning("Enrollment confirmation mail failed: {$e->getMessage()}");
        }

        return back()->with('enrolled', $course->title);
    }
}
