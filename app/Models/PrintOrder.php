<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintOrder extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'catalog_item_id',
        'file_path',
        'file_name',
        'material',
        'color',
        'quantity',
        'notes',
        'status',
        'quote_details',
        'quoted_price',
        'quoted_time',
    ];

    protected $casts = [
        'quoted_price' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(PrintCatalog::class, 'catalog_item_id');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['received', 'quoting']);
    }
}
