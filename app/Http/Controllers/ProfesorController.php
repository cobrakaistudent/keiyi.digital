<?php

namespace App\Http\Controllers;

use App\Mail\StudentInvitation;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProfesorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $students = $user->students()->get();
        $courses = Course::published()->orderBy('sort_order')->get();

        $studentProgress = [];
        foreach ($students as $student) {
            $enrollments = DB::table('enrollments')
                ->where('user_id', $student->id)
                ->get()
                ->keyBy('course_id');

            $studentProgress[$student->id] = [
                'user' => $student,
                'enrollments' => $enrollments,
            ];
        }

        return view('profesor.dashboard', [
            'user' => $user,
            'students' => $students,
            'courses' => $courses,
            'studentProgress' => $studentProgress,
        ]);
    }

    public function students()
    {
        $user = Auth::user();
        $students = $user->students()->get();
        $courses = Course::published()->orderBy('sort_order')->get();

        return view('profesor.students', [
            'user' => $user,
            'students' => $students,
            'courses' => $courses,
        ]);
    }

    public function addStudents(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'course_slug' => ['required', 'string', 'exists:courses,slug'],
            'students' => ['required', 'array', 'min:1'],
            'students.*.name' => ['required', 'string', 'max:255'],
            'students.*.email' => ['required', 'email', 'max:255'],
        ]);

        $remaining = $user->remainingStudentSlots();
        $newStudents = collect($request->students)->filter(fn ($s) => ! empty($s['name']) && ! empty($s['email']));

        if ($newStudents->count() > $remaining) {
            return back()->withErrors([
                'students' => "Solo puedes agregar {$remaining} alumnos más (límite: {$user->student_limit}).",
            ])->withInput();
        }

        $courseSlug = $request->course_slug;
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $added = 0;

        foreach ($newStudents as $studentData) {
            $student = User::where('email', $studentData['email'])->first();
            $isNewAccount = false;
            $tempPassword = null;

            if (! $student) {
                $tempPassword = Str::random(10);
                $isNewAccount = true;
                $student = User::create([
                    'name' => $studentData['name'],
                    'email' => $studentData['email'],
                    'password' => Hash::make($tempPassword),
                    'role' => 'student',
                    'approval_status' => 'approved',
                    'profesor_id' => $user->id,
                    'email_verified_at' => now(),
                ]);
            } else {
                if (! $student->profesor_id) {
                    $student->update(['profesor_id' => $user->id]);
                }
            }

            $exists = DB::table('enrollments')
                ->where('user_id', $student->id)
                ->where('course_id', $courseSlug)
                ->exists();

            if (! $exists) {
                DB::table('enrollments')->insert([
                    'user_id' => $student->id,
                    'course_id' => $courseSlug,
                    'progress_percent' => 0,
                    'enrolled_at' => now(),
                    'enrolled_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $added++;

                // Enviar email de invitación si es cuenta nueva
                if ($isNewAccount && $tempPassword) {
                    try {
                        Mail::to($student->email)->send(new StudentInvitation(
                            studentName: $student->name,
                            studentEmail: $student->email,
                            profesorName: $user->fullName(),
                            courseTitle: $course->title,
                            tempPassword: $tempPassword,
                            loginUrl: url('/login'),
                        ));
                    } catch (\Throwable $e) {
                        \Log::warning("Student invitation mail failed for {$student->email}: {$e->getMessage()}");
                    }
                }
            }
        }

        return back()->with('success', "{$added} alumno(s) inscrito(s) exitosamente.");
    }
}
