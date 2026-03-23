<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AcademiaStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        // Only show on EnrollmentResource page, not main dashboard
        return request()->routeIs('filament.admin.resources.enrollments.*');
    }

    protected function getStats(): array
    {
        $totalStudents = User::where('role', 'student')
            ->where('approval_status', 'approved')
            ->count();

        $totalEnrollments = Enrollment::count();

        $avgProgress = Enrollment::count() > 0
            ? round(Enrollment::avg('progress_percent'), 1)
            : 0;

        $completedCourses = Enrollment::where('progress_percent', '>=', 100)->count();

        $totalCompletions = LessonCompletion::count();

        $activeCourses = Course::published()->count();

        return [
            Stat::make('Alumnos Aprobados', $totalStudents)
                ->description('Con acceso a la academia')
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Inscripciones Totales', $totalEnrollments)
                ->description("En {$activeCourses} cursos activos")
                ->color('info')
                ->icon('heroicon-o-clipboard-document-list'),

            Stat::make('Avance Promedio', $avgProgress.'%')
                ->description("{$completedCourses} cursos completados al 100%")
                ->color($avgProgress >= 50 ? 'success' : 'warning')
                ->icon('heroicon-o-chart-bar'),

            Stat::make('Lecciones Completadas', $totalCompletions)
                ->description('Total acumulado')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
