<?php

namespace App\Filament\Widgets;

use App\Models\BusinessCost;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpenseStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Expense::grandTotal();
        $thisMonth = Expense::whereMonth('date', now()->month)->whereYear('date', now()->year)->sum('amount_mxn');
        $monthlyRecurring = array_sum(array_filter(BusinessCost::monthlyByCategory(), fn ($v, $k) => $k !== 'total', ARRAY_FILTER_USE_BOTH));
        $usdMxn = BusinessCost::getUsdToMxn();

        return [
            Stat::make('Gasto Total Acumulado', '$' . number_format($total, 0) . ' MXN')
                ->description('Desde el inicio del proyecto')
                ->color('danger')
                ->icon('heroicon-o-banknotes'),
            Stat::make('Gasto Este Mes', '$' . number_format($thisMonth, 0) . ' MXN')
                ->description(now()->format('F Y'))
                ->color('warning')
                ->icon('heroicon-o-calendar'),
            Stat::make('Costos Fijos Mensuales', '$' . number_format($monthlyRecurring, 0) . ' MXN')
                ->description('Hosting + IA + Luz (recurrente)')
                ->color('info')
                ->icon('heroicon-o-arrow-path'),
            Stat::make('Tipo de Cambio', '$' . number_format($usdMxn, 2) . ' MXN/USD')
                ->description('Actualizado diariamente')
                ->color('gray')
                ->icon('heroicon-o-currency-dollar'),
        ];
    }
}
