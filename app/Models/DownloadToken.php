<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DownloadToken extends Model
{
    protected $fillable = [
        'catalog_item_id',
        'email',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(PrintCatalog::class, 'catalog_item_id');
    }

    public static function generate(PrintCatalog $item, string $email): self
    {
        return self::create([
            'catalog_item_id' => $item->id,
            'email'           => $email,
            'token'           => Str::random(64),
            'expires_at'      => now()->addHours(24),
        ]);
    }

    public function isValid(): bool
    {
        return is_null($this->used_at) && $this->expires_at->isFuture();
    }

    public function markUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
