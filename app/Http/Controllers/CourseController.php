<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function show(string $courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->where('is_published', true)->firstOrFail();
        $user = Auth::user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseSlug)
            ->first();

        if (! $enrollment) {
            return redirect()->route('academia.dashboard')->with('error', 'Debes inscribirte primero.');
        }

        $lessons = $course->publishedLessons;
        $completedIds = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        return view('academia.course.show', compact('course', 'lessons', 'enrollment', 'completedIds'));
    }

    public function lessonShow(string $courseSlug, string $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->where('is_published', true)->firstOrFail();
        $user = Auth::user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $courseSlug)
            ->first();

        if (! $enrollment) {
            return redirect()->route('academia.dashboard')->with('error', 'Debes inscribirte primero.');
        }

        $lessons = $course->publishedLessons;
        $lesson = $lessons->where('slug', $lessonSlug)->firstOrFail();

        $completedIds = LessonCompletion::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->pluck('lesson_id')
            ->toArray();

        $currentIndex = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $prev = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $next = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        $completion = LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        return view('academia.course.lesson', compact('course', 'lesson', 'lessons', 'prev', 'next', 'completion', 'completedIds'));
    }

    public function markComplete(Request $request, string $courseSlug, string $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->where('is_published', true)->firstOrFail();
        $lesson = Lesson::where('course_id', $course->id)->where('slug', $lessonSlug)->firstOrFail();
        $user = Auth::user();

        $this->ensureEnrolled($user->id, $courseSlug);

        LessonCompletion::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        $this->recalculateProgress($user->id, $course);

        $lessons = $course->publishedLessons;
        $currentIndex = $lessons->search(fn ($l) => $l->id === $lesson->id);
        $next = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        if ($next) {
            return redirect()->route('academia.curso.leccion', [$courseSlug, $next->slug])
                ->with('completed', $lesson->title);
        }

        return redirect()->route('academia.curso', $courseSlug)
            ->with('completed', $lesson->title);
    }

    public function submitQuiz(Request $request, string $courseSlug, string $lessonSlug)
    {
        $course = Course::where('slug', $courseSlug)->where('is_published', true)->firstOrFail();
        $lesson = Lesson::where('course_id', $course->id)->where('slug', $lessonSlug)->firstOrFail();
        $user = Auth::user();

        $this->ensureEnrolled($user->id, $courseSlug);

        if (! $lesson->quiz_data || ! is_array($lesson->quiz_data)) {
            abort(404);
        }

        $answers = $request->input('answers', []);
        $questions = $lesson->quiz_data;
        $correct = 0;
        $results = [];

        foreach ($questions as $i => $q) {
            if (! is_array($q) || ! isset($q['question'], $q['options'], $q['correct'])) {
                continue;
            }
            $userAnswer = $answers[$i] ?? null;
            $isCorrect = (int) $userAnswer === (int) $q['correct'];
            if ($isCorrect) {
                $correct++;
            }
            $results[] = [
                'question' => $q['question'],
                'options' => $q['options'],
                'correct' => $q['correct'],
                'user_answer' => $userAnswer !== null ? (int) $userAnswer : null,
                'is_correct' => $isCorrect,
                'explanation' => $q['explanation'] ?? '',
            ];
        }

        $total = count($questions);
        $score = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed = $score >= $lesson->pass_threshold;

        if ($passed) {
            LessonCompletion::updateOrCreate(
                ['user_id' => $user->id, 'lesson_id' => $lesson->id],
                ['score' => $score, 'completed_at' => now()]
            );
            $this->recalculateProgress($user->id, $course);
        }

        return view('academia.course.quiz-results', compact(
            'course', 'lesson', 'results', 'score', 'correct', 'total', 'passed'
        ));
    }

    private function recalculateProgress(int $userId, Course $course): void
    {
        $totalLessons = $course->publishedLessons()->count();
        if ($totalLessons === 0) {
            return;
        }

        $completedLessons = LessonCompletion::where('user_id', $userId)
            ->whereIn('lesson_id', $course->publishedLessons()->pluck('lessons.id'))
            ->count();

        $progress = round(($completedLessons / $totalLessons) * 100);

        DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('course_id', $course->slug)
            ->update(['progress_percent' => $progress, 'updated_at' => now()]);
    }

    private function ensureEnrolled(int $userId, string $courseSlug): void
    {
        $enrolled = Enrollment::where('user_id', $userId)
            ->where('course_id', $courseSlug)
            ->exists();

        if (! $enrolled) {
            abort(403, 'Debes estar inscrito en este curso.');
        }
    }
}
