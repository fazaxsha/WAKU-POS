<?php

// ============================================================
// app/Http/Controllers/PurchaseController.php  — index update
// ============================================================
 
namespace App\Http\Controllers;
 
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Traits\Searchable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
 
class PurchaseController extends Controller
{
    use Searchable;
 
    public function index(Request $request): View
    {
        $query = Purchase::with(['supplier', 'user'])->latest('purchase_date');
 
        $this->applySearch(
            $query,
            $request,
            filterColumns: [
                'status'      => 'status',
                'supplier_id' => 'supplier_id',
            ],
            dateRange: [
                'from_key' => 'from',
                'to_key'   => 'to',
                'column'   => 'purchase_date',
            ]
        );
 
        $purchases = $query->paginate($this->perPage($request))->withQueryString();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
 
        return view('purchases.index', compact('purchases', 'suppliers'));
    }
 
    public function create(): View
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products  = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }
 
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'purchase_date'        => 'required|date',
            'reference_no'         => 'nullable|string|max:100|unique:purchases,reference_no',
            'notes'                => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.unit_cost'    => 'required|numeric|min:0',
        ]);
 
        $items     = $request->items;
        $totalCost = 0;
        $itemRows  = [];
 
        foreach ($items as $item) {
            $subtotal   = $item['qty'] * $item['unit_cost'];
            $totalCost += $subtotal;
            $itemRows[] = [
                'product_id' => $item['product_id'],
                'qty'        => $item['qty'],
                'unit_cost'  => $item['unit_cost'],
                'subtotal'   => $subtotal,
            ];
        }
 
        $purchase = Purchase::create([
            'supplier_id'   => $request->supplier_id,
            'user_id'       => auth()->id(),
            'reference_no'  => $request->reference_no,
            'total_cost'    => $totalCost,
            'status'        => 'pending',
            'notes'         => $request->notes,
            'purchase_date' => $request->purchase_date,
        ]);
 
        $purchase->items()->createMany($itemRows);
 
        return redirect()->route('purchases.index')->with('success', 'Purchase order berhasil dibuat.');
    }
 
    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'user', 'items.product']);
        return view('purchases.show', compact('purchase'));
    }
 
    public function edit(Purchase $purchase): View
    {
        if ($purchase->status !== 'pending') {
            return redirect()->route('purchases.show', $purchase)
                ->with('error', 'Purchase order yang sudah diproses tidak dapat diedit.');
        }
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products  = Product::with('category')->where('is_active', true)->orderBy('name')->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }
 
    public function update(Request $request, Purchase $purchase): RedirectResponse
    {
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'Purchase order yang sudah diproses tidak dapat diedit.');
        }
 
        $request->validate([
            'supplier_id'        => 'required|exists:suppliers,id',
            'purchase_date'      => 'required|date',
            'reference_no'       => 'nullable|string|max:100|unique:purchases,reference_no,' . $purchase->id,
            'notes'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.unit_cost'  => 'required|numeric|min:0',
        ]);
 
        $totalCost = 0;
        $itemRows  = [];
        foreach ($request->items as $item) {
            $subtotal   = $item['qty'] * $item['unit_cost'];
            $totalCost += $subtotal;
            $itemRows[] = ['product_id' => $item['product_id'], 'qty' => $item['qty'], 'unit_cost' => $item['unit_cost'], 'subtotal' => $subtotal];
        }
 
        $purchase->update([
            'supplier_id'   => $request->supplier_id,
            'reference_no'  => $request->reference_no,
            'total_cost'    => $totalCost,
            'notes'         => $request->notes,
            'purchase_date' => $request->purchase_date,
        ]);
 
        $purchase->items()->delete();
        $purchase->items()->createMany($itemRows);
 
        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase order berhasil diperbarui.');
    }
 
    public function destroy(Purchase $purchase): RedirectResponse
    {
        if ($purchase->status === 'received') {
            return back()->with('error', 'Purchase order yang sudah diterima tidak dapat dihapus.');
        }
        $purchase->items()->delete();
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Purchase order berhasil dihapus.');
    }
 
    public function confirm(Purchase $purchase): RedirectResponse
    {
        if ($purchase->status !== 'pending') {
            return back()->with('error', 'Purchase order ini sudah diproses sebelumnya.');
        }

        $purchase->load('items');

        try {
            DB::transaction(function () use ($purchase) {
                foreach ($purchase->items as $item) {
                    $product   = Product::lockForUpdate()->find($item->product_id);
                    $qtyBefore = $product->stock_qty;

                    DB::statement(
                        'UPDATE products SET stock_qty = stock_qty + ?, buy_price = ?, updated_at = ? WHERE id = ?',
                        [$item->qty, $item->unit_cost, now(), $item->product_id]
                    );

                    StockMovement::record(
                        productId:     $item->product_id,
                        userId:        auth()->id(),
                        type:          'purchase',
                        qtyBefore:     $qtyBefore,
                        qtyChange:     $item->qty,
                        referenceType: Purchase::class,
                        referenceId:   $purchase->id,
                        notes:         "Pembelian PO #{$purchase->reference_no}",
                    );
                }

                $purchase->update(['status' => 'received']);
            });
        } catch (\Exception $e) {
            Log::error('Purchase confirm failed: ' . $e->getMessage());

            return back()->with('error', 'Gagal mengonfirmasi purchase order. Silakan coba lagi.');
        }

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase order dikonfirmasi. Stok produk telah diperbarui.');
    }
}