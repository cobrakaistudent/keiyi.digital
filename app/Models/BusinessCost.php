<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCost extends Model
{
    protected $fillable = [
        'name', 'category', 'amount', 'currency', 'frequency',
        'notes', 'url', 'active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Costo mensual normalizado (convierte yearly y one_time a mensual)
     */
    public function getMonthlyCostAttribute(): float
    {
        return match($this->frequency) {
            'yearly'   => $this->amount / 12,
            'one_time' => 0, // no se prorratea
            default    => $this->amount,
        };
    }

    /**
     * Totales por categoría
     */
    public static function monthlyByCategory(): array
    {
        $costs = self::active()->get();
        $result = [];
        foreach ($costs as $cost) {
            $cat = $cost->category;
            $result[$cat] = ($result[$cat] ?? 0) + $cost->monthly_cost;
        }
        $result['total'] = array_sum($result);
        return $result;
    }
}
