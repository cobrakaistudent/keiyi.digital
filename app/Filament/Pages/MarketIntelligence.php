<?php

namespace App\Filament\Pages;

use App\Models\BusinessCost;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Filament\Pages\Page;

class MarketIntelligence extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Inteligencia de Mercado';
    protected static ?string $navigationGroup = 'Economía';
    protected static ?int $navigationSort = 5;
    protected static ?string $title = 'Inteligencia de Mercado — EdTech LATAM';

    protected static string $view = 'filament.pages.market-intelligence';

    public function getViewData(): array
    {
        $monthlyCost = BusinessCost::active()->get()->sum(fn ($c) => $c->monthly_cost);
        $totalCourses = Course::where('is_published', true)->count();
        $totalLessons = Lesson::where('is_published', true)->count();
        $totalStudents = User::where('role', 'student')->count();
        $approvedStudents = User::where('role', 'student')->where('approval_status', 'approved')->count();

        return [
            'monthlyCost' => $monthlyCost,
            'totalCourses' => $totalCourses,
            'totalLessons' => $totalLessons,
            'totalStudents' => $totalStudents,
            'approvedStudents' => $approvedStudents,
            'breakeven_general' => $monthlyCost > 0 ? ceil($monthlyCost / 199.99) : 0,
            'breakeven_student' => $monthlyCost > 0 ? ceil($monthlyCost / 49.99) : 0,
        ];
    }
}
