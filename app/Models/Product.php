<?php
// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'sku', 'description',
        'sell_price', 'wholesale_price', 'wholesale_min_qty', 'buy_price', 'stock_qty', 'stock_min',
        'unit', 'image', 'is_active',
    ];

    protected $casts = [
        'sell_price'        => 'decimal:2',
        'wholesale_price'   => 'decimal:2',
        'buy_price'         => 'decimal:2',
        'stock_qty'         => 'decimal:3',
        'stock_min'         => 'decimal:3',
        'wholesale_min_qty' => 'decimal:3',
        'is_active'         => 'boolean',
    ];

    // ── Relations ────────────────────────────────────
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // ── Scopes ───────────────────────────────────────
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stock_qty', '<=', 'stock_min');
    }

    // ── Accessors ────────────────────────────────────
    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_qty <= $this->stock_min;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/no-image.png');
    }
}
