<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use App\Models\Enrollment;
use Filament\Widgets\ChartWidget;

class CourseProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Avance por Curso';

    protected static ?int $sort = 4;

    protected static ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.resources.enrollments.*');
    }

    protected function getData(): array
    {
        $courses = Course::published()->orderBy('sort_order')->get();

        $labels = [];
        $enrolled = [];
        $avgProgress = [];

        foreach ($courses as $course) {
            $labels[] = $course->emoji.' '.$course->title;

            $enrollments = Enrollment::where('course_id', $course->slug)->get();
            $enrolled[] = $enrollments->count();
            $avgProgress[] = $enrollments->count() > 0
                ? round($enrollments->avg('progress_percent'), 1)
                : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inscritos',
                    'data' => $enrolled,
                    'backgroundColor' => 'rgba(251, 191, 36, 0.7)',
                    'borderColor' => 'rgb(251, 191, 36)',
                ],
                [
                    'label' => 'Avance promedio (%)',
                    'data' => $avgProgress,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
