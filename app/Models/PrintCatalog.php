<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrintCatalog extends Model
{
    protected $table = 'print_catalog';

    protected $fillable = [
        'title',
        'description',
        'embed_url',
        'file_path',
        'file_name',
        'price',
        'material',
        'print_time',
        'downloadable',
        'orderable',
        'active',
    ];

    protected $casts = [
        'downloadable' => 'boolean',
        'orderable'    => 'boolean',
        'active'       => 'boolean',
        'price'        => 'decimal:2',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(PrintOrder::class, 'catalog_item_id');
    }

    public function downloadTokens(): HasMany
    {
        return $this->hasMany(DownloadToken::class, 'catalog_item_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
