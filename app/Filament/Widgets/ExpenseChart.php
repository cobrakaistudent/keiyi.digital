<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;

class ExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Gastos por Mes (MXN)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Expense::totalByMonth();

        if (empty($data)) {
            return [
                'datasets' => [['label' => 'Gastos', 'data' => [0]]],
                'labels' => ['Sin datos'],
            ];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Gastos MXN',
                    'data' => array_values($data),
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
