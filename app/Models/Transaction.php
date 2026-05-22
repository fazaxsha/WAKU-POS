<?php
// app/Models/Transaction.php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class Transaction extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'user_id', 'invoice_no', 'total_amount',
        'discount', 'paid_amount', 'payment_method',
        'notes', 'transaction_date',
    ];
 
    protected $casts = [
        'total_amount'     => 'decimal:2',
        'discount'         => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'transaction_date' => 'datetime',
    ];
 
    // ── Relations ────────────────────────────────────
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
 
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
 
    // ── Accessors ────────────────────────────────────
    public function getChangeAttribute(): float
    {
        return (float) $this->paid_amount - (float) $this->total_amount;
    }
 
    // ── Static helpers ───────────────────────────────
    public static function generateInvoice(): string
    {
        $today = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count();

        do {
            $count++;
            $invoice = 'INV-' . $today . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        } while (self::where('invoice_no', $invoice)->exists());

        return $invoice;
    }
}