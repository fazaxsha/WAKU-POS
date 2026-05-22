<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockOpnameRequest;
use App\Models\Product;
use App\Models\StockOpname;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockOpnameController extends Controller
{
    public function index(Request $request): View
    {
        $opnames = StockOpname::with(['user', 'items'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('stock-opnames.index', compact('opnames'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'stock_qty']);

        // Pre-encode to JSON here to avoid Blade @json() ParseError
        // when using arrow functions inside the directive.
        $productsJson = $products->map(function ($p) {
            return [
                'id'        => $p->id,
                'name'      => $p->name,
                'sku'       => $p->sku,
                'stock_qty' => $p->stock_qty,
            ];
        })->toJson();

        return view('stock-opnames.create', compact('products', 'productsJson'));
    }

    public function store(StoreStockOpnameRequest $request): RedirectResponse
    {
        try {
            $opname = DB::transaction(function () use ($request) {
                $opname = StockOpname::create([
                    'user_id'   => auth()->id(),
                    'opname_no' => StockOpname::generateOpnameNo(),
                    'status'    => 'draft',
                    'notes'     => $request->notes,
                ]);

                foreach ($request->items as $item) {
                    $product   = Product::findOrFail($item['product_id']);
                    $systemQty = $product->stock_qty;
                    $actualQty = (int) $item['actual_qty'];

                    $opname->items()->create([
                        'product_id' => $product->id,
                        'system_qty' => $systemQty,
                        'actual_qty' => $actualQty,
                        'difference' => $actualQty - $systemQty,
                        'notes'      => $item['notes'] ?? null,
                    ]);
                }

                return $opname;
            });
        } catch (\Exception $e) {
            Log::error('Stock opname store failed: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal membuat stok opname: ' . $e->getMessage());
        }

        return redirect()->route('stock-opnames.show', $opname)
            ->with('success', 'Draft stok opname berhasil dibuat.');
    }

    public function show(StockOpname $stockOpname): View
    {
        $stockOpname->load(['user', 'items.product']);
        return view('stock-opnames.show', compact('stockOpname'));
    }

    public function confirm(StockOpname $stockOpname): RedirectResponse
    {
        if ($stockOpname->status !== 'draft') {
            return back()->with('error', 'Stok opname ini sudah dikonfirmasi atau dibatalkan.');
        }

        $stockOpname->load('items.product');

        try {
            DB::transaction(function () use ($stockOpname) {
                foreach ($stockOpname->items as $item) {
                    $product  = Product::lockForUpdate()->find($item->product_id);
                    $oldStock = $product->stock_qty;
                    $newStock = $item->actual_qty;

                    // Update product stock
                    $product->update(['stock_qty' => $newStock]);

                    // Record stock movement
                    StockMovement::record(
                        productId:     $product->id,
                        userId:        auth()->id(),
                        type:          'opname',
                        qtyBefore:     $oldStock,
                        qtyChange:     $newStock - $oldStock,
                        referenceType: StockOpname::class,
                        referenceId:   $stockOpname->id,
                        notes:         "Stok opname #{$stockOpname->opname_no}",
                    );
                }

                $stockOpname->update([
                    'status'       => 'confirmed',
                    'confirmed_at' => now(),
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Stock opname confirm failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengonfirmasi stok opname.');
        }

        return redirect()->route('stock-opnames.show', $stockOpname)
            ->with('success', 'Stok opname berhasil dikonfirmasi. Stok produk telah diperbarui.');
    }

    public function cancel(StockOpname $stockOpname): RedirectResponse
    {
        if ($stockOpname->status !== 'draft') {
            return back()->with('error', 'Stok opname ini sudah dikonfirmasi atau dibatalkan.');
        }

        $stockOpname->update(['status' => 'cancelled']);

        return redirect()->route('stock-opnames.index')
            ->with('success', 'Stok opname dibatalkan.');
    }
}
