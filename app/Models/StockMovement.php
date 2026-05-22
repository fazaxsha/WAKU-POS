<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'type',
        'qty_before', 'qty_change', 'qty_after',
        'reference_type', 'reference_id', 'notes',
    ];

    protected $casts = [
        'qty_before' => 'decimal:3',
        'qty_change' => 'decimal:3',
        'qty_after'  => 'decimal:3',
    ];

    // ── Relations ────────────────────────────────────
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Static helper to record movement ─────────────
    public static function record(
        int        $productId,
        int        $userId,
        string     $type,
        int|float  $qtyBefore,
        int|float  $qtyChange,
        ?string $referenceType = null,
        ?int    $referenceId = null,
        ?string $notes = null,
    ): self {
        return self::create([
            'product_id'     => $productId,
            'user_id'        => $userId,
            'type'           => $type,
            'qty_before'     => $qtyBefore,
            'qty_change'     => $qtyChange,
            'qty_after'      => $qtyBefore + $qtyChange,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'notes'          => $notes,
        ]);
    }
}
