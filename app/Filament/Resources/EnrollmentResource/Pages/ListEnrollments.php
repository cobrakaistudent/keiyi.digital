<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use Filament\Resources\Pages\ListRecords;

class ListEnrollments extends ListRecords
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\AcademiaStatsWidget::class,
            \App\Filament\Widgets\CourseProgressChart::class,
        ];
    }
}
