<?php

namespace Database\Seeders;

use App\Models\BusinessCost;
use App\Models\PricingConfig;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // COSTOS OPERATIVOS MENSUALES
        // =====================================================================

        $costs = [
            // Hosting
            ['name' => 'Hostinger (Business Plan)', 'category' => 'hosting', 'amount' => 149, 'currency' => 'MXN', 'frequency' => 'monthly', 'url' => 'https://www.hostinger.mx'],
            ['name' => 'Dominio keiyi.digital', 'category' => 'hosting', 'amount' => 250, 'currency' => 'MXN', 'frequency' => 'yearly'],

            // Herramientas IA
            ['name' => 'Claude Pro (Anthropic)', 'category' => 'ai_tools', 'amount' => 20, 'currency' => 'USD', 'frequency' => 'monthly', 'url' => 'https://claude.ai'],
            ['name' => 'Gemini Advanced (Google)', 'category' => 'ai_tools', 'amount' => 20, 'currency' => 'USD', 'frequency' => 'monthly', 'url' => 'https://gemini.google.com'],

            // Electricidad
            ['name' => 'CFE - Consumo impresora 3D (estimado)', 'category' => 'electricity', 'amount' => 200, 'currency' => 'MXN', 'frequency' => 'monthly', 'notes' => 'Bambu Lab X1C ~350W promedio. Estimado 20hrs/mes de impresión.'],
            ['name' => 'CFE - Mac Mini M2 (24/7)', 'category' => 'electricity', 'amount' => 50, 'currency' => 'MXN', 'frequency' => 'monthly', 'notes' => 'Mac Mini ~15W promedio 24/7 = ~11 kWh/mes'],

            // Filamento (referencia, el detalle va en Inventario Filamentos)
            ['name' => 'Filamento PLA 1kg (referencia)', 'category' => 'filament', 'amount' => 450, 'currency' => 'MXN', 'frequency' => 'per_unit', 'notes' => 'Precio promedio por spool de 1kg. Actualizar con cada compra.'],
        ];

        foreach ($costs as $cost) {
            BusinessCost::updateOrCreate(
                ['name' => $cost['name']],
                array_merge($cost, ['active' => true])
            );
        }

        // =====================================================================
        // FÓRMULA DE PRICING — Variables editables
        // =====================================================================

        $configs = [
            ['key' => 'filament_cost_per_kg',       'value' => '450',   'label' => 'Costo filamento por kg',        'unit' => 'MXN/kg',   'description' => 'Precio promedio del spool de 1kg. Actualizar cuando cambie el proveedor.'],
            ['key' => 'printer_watts',               'value' => '350',   'label' => 'Consumo impresora',             'unit' => 'watts',     'description' => 'Watts promedio de la impresora durante impresión. Bambu X1C: ~350W.'],
            ['key' => 'electricity_cost_per_kwh',    'value' => '1.50',  'label' => 'Costo electricidad por kWh',    'unit' => 'MXN/kWh',  'description' => 'Tarifa CFE promedio para CDMX. Verificar en recibo de luz.'],
            ['key' => 'labor_cost_per_hour',         'value' => '50',    'label' => 'Costo mano de obra por hora',   'unit' => 'MXN/hr',   'description' => 'Incluye: preparación, monitoreo, post-procesado, empaque.'],
            ['key' => 'monthly_overhead',            'value' => '1500',  'label' => 'Overhead mensual total',        'unit' => 'MXN/mes',  'description' => 'Suma de hosting + IA + electricidad base. Se actualiza automáticamente cuando agregas costos.'],
            ['key' => 'work_hours_per_month',        'value' => '160',   'label' => 'Horas de trabajo por mes',      'unit' => 'hrs',      'description' => 'Horas productivas estimadas al mes para prorratear overhead.'],
            ['key' => 'profit_margin_percent',       'value' => '40',    'label' => 'Margen de ganancia',            'unit' => '%',         'description' => 'Porcentaje sobre el costo total. 40% = por cada $100 de costo, cobras $140.'],
            ['key' => 'demand_factor',               'value' => '1.0',   'label' => 'Factor de demanda',             'unit' => 'x',         'description' => '1.0 = normal. 1.2 = alta demanda (+20%). 0.8 = promoción (-20%).'],
        ];

        foreach ($configs as $config) {
            PricingConfig::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
