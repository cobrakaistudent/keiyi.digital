<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;

class ExpenseByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Gastos por Categoría (MXN)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Expense::totalByCategory();

        if (empty($data)) {
            return [
                'datasets' => [['data' => [1], 'backgroundColor' => ['#374151']]],
                'labels' => ['Sin datos'],
            ];
        }

        $colors = [
            'hosting'     => '#6366f1',
            'ai_tools'    => '#3b82f6',
            'filament'    => '#22c55e',
            'electricity' => '#ef4444',
            'equipment'   => '#f59e0b',
            'software'    => '#8b5cf6',
            'marketing'   => '#ec4899',
            'development' => '#14b8a6',
            'legal'       => '#64748b',
            'other'       => '#9ca3af',
        ];

        $labels = [
            'hosting' => 'Hosting', 'ai_tools' => 'IA', 'filament' => 'Filamento',
            'electricity' => 'Luz', 'equipment' => 'Equipo', 'software' => 'Software',
            'marketing' => 'Marketing', 'development' => 'Dev', 'legal' => 'Legal', 'other' => 'Otro',
        ];

        return [
            'datasets' => [
                [
                    'data' => array_values($data),
                    'backgroundColor' => array_map(fn ($k) => $colors[$k] ?? '#9ca3af', array_keys($data)),
                ],
            ],
            'labels' => array_map(fn ($k) => $labels[$k] ?? $k, array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
