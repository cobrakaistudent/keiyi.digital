<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilamentInventory extends Model
{
    protected $table = 'filament_inventory';

    protected $fillable = [
        'brand', 'material', 'color', 'weight_grams', 'remaining_grams',
        'cost_per_kg', 'diameter', 'status', 'purchased_at', 'notes',
    ];

    protected $casts = [
        'cost_per_kg'   => 'decimal:2',
        'purchased_at'  => 'date',
    ];

    public function getCostPerGramAttribute(): float
    {
        return $this->cost_per_kg / 1000;
    }

    public function getRemainingPercentAttribute(): int
    {
        if ($this->weight_grams <= 0) return 0;
        return (int) round(($this->remaining_grams / $this->weight_grams) * 100);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
