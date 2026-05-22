<?php

// ============================================================
// app/Http/Controllers/SupplierController.php  — versi update
// ============================================================
 
namespace App\Http\Controllers;
 
use App\Models\Supplier;
use App\Traits\Searchable;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
 
class SupplierController extends Controller
{
    use Searchable;
 
    public function index(Request $request): View
    {
        $query = Supplier::withCount('purchases');
 
        $this->applySearch(
            $query,
            $request,
            searchColumns: ['name', 'phone', 'email', 'address'],
            filterColumns: ['is_active' => 'is_active'],
        );
 
        $suppliers = $query->orderBy('name')
            ->paginate($this->perPage($request))
            ->withQueryString();
 
        return view('suppliers.index', compact('suppliers'));
    }
 
    public function create(): View { return view('suppliers.create'); }
 
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:150',
            'address'   => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        Supplier::create($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }
 
    public function edit(Supplier $supplier): View { return view('suppliers.edit', compact('supplier')); }
 
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:150',
            'phone'     => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:150',
            'address'   => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }
 
    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($supplier->purchases()->count() > 0) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena memiliki riwayat pembelian.');
        }
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
 
    public function show(Supplier $supplier): RedirectResponse { return redirect()->route('suppliers.index'); }
}