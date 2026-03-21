<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingConfig extends Model
{
    protected $table = 'pricing_config';

    protected $fillable = ['key', 'value', 'label', 'unit', 'description'];

    public static function get(string $key, $default = null): string
    {
        return self::where('key', $key)->value('value') ?? $default;
    }

    public static function getFloat(string $key, float $default = 0): float
    {
        return (float) (self::where('key', $key)->value('value') ?? $default);
    }

    /**
     * Calcula el costo de impresión de un modelo 3D
     */
    public static function calculatePrintCost(float $gramsUsed, float $printHours): array
    {
        // Costos de material
        $costPerKg       = self::getFloat('filament_cost_per_kg', 450);
        $materialCost    = ($gramsUsed / 1000) * $costPerKg;

        // Costos de electricidad
        $printerWatts    = self::getFloat('printer_watts', 350);
        $costPerKwh      = self::getFloat('electricity_cost_per_kwh', 1.50);
        $electricityCost = ($printerWatts / 1000) * $printHours * $costPerKwh;

        // Overhead operativo (prorrateado por hora de trabajo)
        $monthlyOverhead = self::getFloat('monthly_overhead', 0);
        $workHoursMonth  = self::getFloat('work_hours_per_month', 160);
        $overheadPerHour = $workHoursMonth > 0 ? $monthlyOverhead / $workHoursMonth : 0;
        $overheadCost    = $overheadPerHour * $printHours;

        // Mano de obra
        $laborPerHour    = self::getFloat('labor_cost_per_hour', 50);
        $laborCost       = $laborPerHour * $printHours;

        // Costo total
        $totalCost       = $materialCost + $electricityCost + $overheadCost + $laborCost;

        // Margen de ganancia
        $marginPercent   = self::getFloat('profit_margin_percent', 40);
        $margin          = $totalCost * ($marginPercent / 100);

        // Factor de demanda
        $demandFactor    = self::getFloat('demand_factor', 1.0);

        $finalPrice      = ($totalCost + $margin) * $demandFactor;

        return [
            'material'    => round($materialCost, 2),
            'electricity' => round($electricityCost, 2),
            'overhead'    => round($overheadCost, 2),
            'labor'       => round($laborCost, 2),
            'total_cost'  => round($totalCost, 2),
            'margin'      => round($margin, 2),
            'margin_pct'  => $marginPercent,
            'demand_factor' => $demandFactor,
            'final_price' => round($finalPrice, 2),
            'inputs'      => [
                'grams'       => $gramsUsed,
                'hours'       => $printHours,
                'cost_per_kg' => $costPerKg,
                'watts'       => $printerWatts,
                'kwh_rate'    => $costPerKwh,
            ],
        ];
    }
}
