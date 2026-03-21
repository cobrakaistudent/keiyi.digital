<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
     * Tipo de cambio USD→MXN (cache 24 hrs)
     */
    public static function getUsdToMxn(): float
    {
        return Cache::remember('usd_to_mxn', 86400, function () {
            try {
                $response = Http::timeout(10)->get('https://open.er-api.com/v6/latest/USD');
                if ($response->ok()) {
                    return (float) $response->json('rates.MXN');
                }
            } catch (\Exception $e) {
                // Fallback silencioso
            }
            return 20.0; // fallback si falla la API
        });
    }

    /**
     * Monto convertido a MXN
     */
    public function getAmountMxnAttribute(): float
    {
        if ($this->currency === 'USD') {
            return $this->amount * self::getUsdToMxn();
        }
        return $this->amount;
    }

    /**
     * Costo mensual normalizado en MXN
     */
    public function getMonthlyCostAttribute(): float
    {
        $amountMxn = $this->amount_mxn;
        return match($this->frequency) {
            'yearly'   => $amountMxn / 12,
            'one_time' => 0,
            default    => $amountMxn,
        };
    }

    /**
     * Totales por categoría en MXN
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
