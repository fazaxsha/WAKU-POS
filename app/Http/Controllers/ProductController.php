<?php
// ============================================================
// app/Http/Controllers/ProductController.php  — versi update
// ============================================================

namespace App\Http\Controllers;

use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\StockMovement;
use App\Traits\Searchable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use Searchable;

    public function index(Request $request): View
    {
        $query = Product::with('category');

        // Pakai trait Searchable
        $this->applySearch(
            $query,
            $request,
            searchColumns: ['name', 'sku', 'description'],
            filterColumns: [
                'category_id' => 'category_id',
                'is_active'   => 'is_active',
            ]
        );

        // Filter stok kritis
        if ($request->filled('low_stock')) {
            $query->whereColumn('stock_qty', '<=', 'stock_min');
        }

        // Sorting
        $sort  = $request->input('sort', 'name');
        $order = $request->input('order', 'asc');
        $allowedSorts = ['name', 'sell_price', 'stock_qty', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $order === 'desc' ? 'desc' : 'asc');
        }

        $products   = $query->paginate($this->perPage($request))->withQueryString();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // Summary counts untuk filter badges
        $totalActive   = Product::where('is_active', true)->count();
        $totalLowStock = Product::whereColumn('stock_qty', '<=', 'stock_min')->count();

        return view('products.index', compact('products', 'categories', 'totalActive', 'totalLowStock'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        $validated['is_active'] = $request->boolean('is_active', true);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product): View
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        $validated['is_active'] = $request->boolean('is_active', true);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function adjustStock(AdjustStockRequest $request, Product $product): RedirectResponse
    {
        $oldQty = $product->stock_qty;
        $newQty = $oldQty + $request->adjustment;
        if ($newQty < 0) {
            return back()->with('error', 'Stok tidak boleh menjadi negatif.');
        }

        $product->update(['stock_qty' => $newQty]);

        StockMovement::record(
            productId:  $product->id,
            userId:     auth()->id(),
            type:       'adjustment',
            qtyBefore:  $oldQty,
            qtyChange:  $request->adjustment,
            notes:      $request->notes,
        );

        activity('product')
            ->performedOn($product)
            ->causedBy(auth()->user())
            ->withProperties(['old_qty' => $oldQty, 'new_qty' => $newQty])
            ->log('stock_adjusted');

        return back()->with('success', 'Stok berhasil disesuaikan.');
    }

    public function exportPrices()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ProductPriceExport,
            'template-update-harga-produk.xlsx'
        );
    }

    public function importPrices(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\ProductPriceImport,
                $request->file('file')
            );

            return back()->with('success', 'Harga produk berhasil diperbarui secara massal.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengimpor file: ' . $e->getMessage());
        }
    }
}