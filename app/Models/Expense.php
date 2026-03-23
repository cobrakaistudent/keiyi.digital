<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'date', 'description', 'category', 'amount', 'currency',
        'amount_mxn', 'payment_method', 'vendor', 'receipt_url', 'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'amount_mxn' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($expense) {
            if (empty($expense->amount_mxn)) {
                $expense->amount_mxn = $expense->currency === 'USD'
                    ? $expense->amount * BusinessCost::getUsdToMxn()
                    : $expense->amount;
            }
        });
    }

    public static function totalByMonth(): array
    {
        $driver = config('database.default');
        $expr = $driver === 'sqlite'
            ? "strftime('%Y-%m', date) as month"
            : "DATE_FORMAT(date, '%Y-%m') as month";

        return self::selectRaw("{$expr}, SUM(amount_mxn) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
    }

    public static function totalByCategory(): array
    {
        return self::selectRaw('category, SUM(amount_mxn) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category')
            ->toArray();
    }

    public static function grandTotal(): float
    {
        return (float) self::sum('amount_mxn');
    }
}
