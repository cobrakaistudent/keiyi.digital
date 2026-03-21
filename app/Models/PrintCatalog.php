<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PrintCatalog extends Model
{
    protected $table = 'print_catalog';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_path',
        'embed_url',
        'file_path',
        'file_name',
        'price',
        'material',
        'category',
        'print_time',
        'downloadable',
        'orderable',
        'active',
        'status',
    ];

    protected $casts = [
        'downloadable' => 'boolean',
        'orderable'    => 'boolean',
        'active'       => 'boolean',
        'price'        => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title);
            }
        });
    }

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

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('active', true);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->active;
    }
}
