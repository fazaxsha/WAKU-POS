<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'opname_no', 'status', 'notes', 'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    // ── Relations ────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    // ── Helpers ──────────────────────────────────────
    public static function generateOpnameNo(): string
    {
        $today = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count();

        do {
            $count++;
            $no = 'OPN-' . $today . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        } while (self::where('opname_no', $no)->exists());

        return $no;
    }

    public function getTotalDifferenceAttribute(): int
    {
        return $this->items->sum('difference');
    }
}
