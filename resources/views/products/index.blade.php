@extends('layouts.app')

@section('title', 'Produk')
@section('page-title', 'Manajemen Produk')

@section('content')

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 mb-1.5">Daftar Produk</h2>
            <div class="flex gap-2">
                <span class="badge-role badge-active">{{ $totalActive }} aktif</span>
                @if ($totalLowStock > 0)
                    <span class="badge-role badge-inactive">⚠ {{ $totalLowStock }} stok kritis</span>
                @endif
            </div>
        </div>
        @can('product.create')
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('products.export-prices') }}" class="btn-outline-sm" title="Export Template Harga">
                    <i class="bi bi-download"></i> Export
                </a>
                <button type="button" class="btn-outline-sm" onclick="document.getElementById('importModal').showModal()"
                    title="Import Update Harga">
                    <i class="bi bi-upload"></i> Import
                </button>
                <a href="{{ route('products.create') }}" class="btn-amber">
                    <i class="bi bi-plus-lg"></i> Tambah Produk
                </a>
            </div>
        @endcan
    </div>

    {{-- Search bar --}}
    <div class="mb-4">
        <x-search-bar action="{{ route('products.index') }}" placeholder="Cari nama produk atau SKU..." :filters="[
            [
                'name' => 'category_id',
                'label' => 'Semua Kategori',
                'type' => 'select',
                'options' => $categories->pluck('name', 'id')->toArray(),
            ],
            [
                'name' => 'is_active',
                'label' => 'Semua Status',
                'type' => 'select',
                'options' => ['1' => 'Aktif', '0' => 'Nonaktif'],
            ],
            ['name' => 'low_stock', 'label' => 'Stok', 'type' => 'select', 'options' => ['1' => 'Stok Kritis']],
        ]" />
    </div>

    {{-- Sort bar --}}
    <div class="flex flex-wrap items-center gap-2 mb-4 text-xs text-slate-500">
        <span class="font-medium">Urutkan:</span>
        @php
            $sorts = ['name' => 'Nama', 'sell_price' => 'Harga', 'stock_qty' => 'Stok', 'created_at' => 'Terbaru'];
            $currentSort = request('sort', 'name');
            $currentOrder = request('order', 'asc');
        @endphp
        @foreach ($sorts as $key => $label)
            <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'order' => $currentSort === $key && $currentOrder === 'asc' ? 'desc' : 'asc', 'page' => 1]) }}"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full border text-xs font-medium transition-all
            {{ $currentSort === $key
                ? 'bg-slate-900 text-white border-slate-900'
                : 'bg-white text-slate-600 border-slate-200 hover:border-teal-300 hover:text-teal-600' }}">
                {{ $label }}
                @if ($currentSort === $key)
                    <i class="bi bi-arrow-{{ $currentOrder === 'asc' ? 'up' : 'down' }}-short"></i>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>SKU</th>
                        <th>Kategori</th>
                        <th>Harga Jual</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th class="text-right" style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="group">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg overflow-hidden shrink-0 bg-slate-50 border border-slate-200 flex items-center justify-center text-lg">
                                        @if ($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt=""
                                                class="w-full h-full object-cover">
                                        @else
                                            📦
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">{{ $product->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $product->category->name ?? '--' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="font-mono text-xs text-slate-500">{{ $product->sku }}</span></td>
                            <td class="text-slate-600">{{ $product->category->name ?? '--' }}</td>
                            <td class="font-semibold text-slate-900">Rp
                                {{ number_format($product->sell_price, 0, ',', '.') }}</td>
                            <td>
                                <span
                                    class="font-bold {{ $product->is_low_stock ? 'text-red-600' : 'text-slate-900' }}">{{ (float) $product->stock_qty }}</span>
                                @if ($product->is_low_stock)
                                    <div class="text-[10px] text-red-500">⚠ min {{ (float) $product->stock_min }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($product->is_active)
                                    <span class="badge-role badge-active">Aktif</span>
                                @else
                                    <span class="badge-role badge-inactive">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('product.edit')
                                        <button type="button" class="topbar-btn" title="Update Stok"
                                            onclick="document.getElementById('stockModal{{ $product->id }}').showModal()">
                                            <i class="bi bi-layers text-xs"></i>
                                        </button>
                                        <a href="{{ route('products.edit', $product) }}" class="topbar-btn" title="Edit">
                                            <i class="bi bi-pencil text-xs"></i>
                                        </a>
                                    @endcan
                                    @can('product.delete')
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                            onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="topbar-btn hover:!text-red-600 hover:!bg-red-50 hover:!border-red-200"
                                                title="Hapus">
                                                <i class="bi bi-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="py-16 text-center text-slate-400">
                                    <i class="bi bi-box-seam block text-4xl mb-3 text-slate-300"></i>
                                    <span class="text-sm">
                                        @if (request('search'))
                                            Tidak ada produk yang cocok dengan "<strong
                                                class="text-slate-600">{{ request('search') }}</strong>"
                                        @else
                                            Tidak ada produk ditemukan
                                        @endif
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $products->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    {{-- Modal Import Excel (native <dialog>) --}}
    <dialog id="importModal"
        class="rounded-2xl shadow-2xl border-0 p-0 w-full max-w-md backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm">
        <form method="POST" action="{{ route('products.import-prices') }}" enctype="multipart/form-data">
            @csrf
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-base">Import Update Harga</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 text-xl leading-none"
                    onclick="document.getElementById('importModal').close()">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-500 mb-5 leading-relaxed">
                    Unggah file Excel (.xlsx) yang telah diunduh dan isi kolom <strong class="text-slate-700">"Harga
                        Baru"</strong>. Kosongkan kolom jika harga tidak berubah.
                </p>
                <label class="form-label">File Excel</label>
                <input type="file" name="file" required accept=".xlsx,.xls,.csv"
                    class="block w-full text-sm text-slate-600
               file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
               file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700
               hover:file:bg-teal-100 file:transition-colors
               border border-slate-200 rounded-xl p-2 cursor-pointer bg-white" />
                <p class="form-text mt-2"><i class="bi bi-info-circle"></i> Maks. ukuran file 2MB.</p>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 rounded-b-2xl">
                <button type="button"
                    class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors"
                    onclick="document.getElementById('importModal').close()">Batal</button>
                <button type="submit" class="btn-amber">
                    <i class="bi bi-upload"></i> Upload &amp; Update
                </button>
            </div>
        </form>
    </dialog>

    {{-- Stock Adjust Modals (one per product) --}}
    @can('product.edit')
        @foreach ($products as $product)
            <dialog id="stockModal{{ $product->id }}"
                class="rounded-2xl shadow-2xl border-0 p-0 w-full max-w-sm backdrop:bg-slate-900/50 backdrop:backdrop-blur-sm">
                <form method="POST" action="{{ route('products.adjust-stock', $product) }}"
                    onsubmit="return validateStockModal({{ $product->id }}, {{ (float) $product->stock_qty }})">
                    @csrf
                    <input type="hidden" name="adjustment" id="modalAdj{{ $product->id }}" value="1">

                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Update Stok</div>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $product->name }}</div>
                        </div>
                        <button type="button" class="text-slate-400 hover:text-slate-600 text-xl leading-none"
                            onclick="document.getElementById('stockModal{{ $product->id }}').close()">&times;</button>
                    </div>

                    {{-- Body --}}
                    <div class="p-5">

                        {{-- Current stock indicator --}}
                        <div class="flex items-center justify-between mb-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-xs text-slate-500 font-medium">Stok saat ini</span>
                            <span
                                class="text-base font-bold font-mono {{ $product->is_low_stock ? 'text-red-600' : 'text-teal-600' }}">
                                {{ (float) $product->stock_qty }}
                            </span>
                        </div>

                        {{-- Toggle --}}
                        <div class="flex gap-2 mb-4">
                            <button type="button" id="mBtnAdd{{ $product->id }}"
                                onclick="setModalType({{ $product->id }}, {{ (float) $product->stock_qty }}, 'add')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 text-sm font-semibold transition-all
                 border-teal-600 bg-teal-600 text-white">
                                <i class="bi bi-plus-lg"></i> Tambah
                            </button>
                            <button type="button" id="mBtnRed{{ $product->id }}"
                                onclick="setModalType({{ $product->id }}, {{ (float) $product->stock_qty }}, 'reduce')"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-lg border-2 text-sm font-semibold transition-all
                 border-slate-200 bg-white text-slate-400">
                                <i class="bi bi-dash-lg"></i> Kurangi
                            </button>
                        </div>

                        {{-- Qty input --}}
                        <div class="flex items-center gap-2 mb-3">
                            <button type="button"
                                onclick="changeModalQty({{ $product->id }}, {{ (float) $product->stock_qty }}, -1)"
                                class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:border-teal-500 hover:text-teal-600 flex items-center justify-center text-lg font-bold transition-all">−</button>
                            <input type="number" id="mQty{{ $product->id }}" value="1" min="1"
                                oninput="refreshModalPreview({{ $product->id }}, {{ (float) $product->stock_qty }})"
                                class="flex-1 border-2 border-teal-500 rounded-lg py-2 text-center text-xl font-bold font-mono outline-none text-slate-900">
                            <button type="button"
                                onclick="changeModalQty({{ $product->id }}, {{ (float) $product->stock_qty }}, 1)"
                                class="w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:border-teal-500 hover:text-teal-600 flex items-center justify-center text-lg font-bold transition-all">+</button>
                        </div>

                        {{-- Quick preset --}}
                        <div class="flex gap-2 mb-4">
                            @foreach ([1, 5, 10, 25] as $p)
                                <button type="button"
                                    onclick="setModalQty({{ $product->id }}, {{ (float) $product->stock_qty }}, {{ $p }})"
                                    class="flex-1 py-1.5 text-xs font-semibold border border-slate-200 rounded-lg bg-white text-slate-500
                 hover:border-teal-500 hover:text-teal-600 transition-all">{{ $p }}</button>
                            @endforeach
                        </div>

                        {{-- Preview --}}
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 bg-slate-50 mb-3">
                            <span class="text-xs text-slate-500">Stok setelah update</span>
                            <span id="mPreview{{ $product->id }}" class="text-base font-bold font-mono text-teal-600">
                                {{ $product->stock_qty + 1 }}
                            </span>
                        </div>

                        {{-- Notes --}}
                        <input type="text" name="notes" placeholder="Catatan (opsional)" maxlength="255"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:border-teal-500 transition-colors">
                    </div>

                    {{-- Footer --}}
                    <div class="px-5 py-4 bg-slate-50 border-t border-slate-100 flex gap-3 rounded-b-2xl">
                        <button type="button"
                            class="flex-1 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-100 transition-colors"
                            onclick="document.getElementById('stockModal{{ $product->id }}').close()">Batal</button>
                        <button type="submit" id="mSubmitBtn{{ $product->id }}"
                            class="flex-1 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </dialog>
        @endforeach
    @endcan

    @push('scripts')
        <script>
            // ── Quick Stock Modal Logic ────────────────────────────────────
            const _modalType = {}; // productId -> 'add' | 'reduce'

            function setModalType(pid, currentStock, type) {
                _modalType[pid] = type;
                const btnAdd = document.getElementById('mBtnAdd' + pid);
                const btnRed = document.getElementById('mBtnRed' + pid);
                if (type === 'add') {
                    btnAdd.className = btnAdd.className.replace(/border-slate-200 bg-white text-slate-400/,
                        'border-teal-600 bg-teal-600 text-white');
                    btnRed.className = btnRed.className.replace(
                        /border-teal-600 bg-teal-600 text-white|border-red-600 bg-red-600 text-white/,
                        'border-slate-200 bg-white text-slate-400');
                    btnAdd.className = btnAdd.className.replace(/border-slate-200 bg-white text-slate-400/,
                        'border-teal-600 bg-teal-600 text-white');
                } else {
                    btnRed.className = btnRed.className.replace(/border-slate-200 bg-white text-slate-400/,
                        'border-red-600 bg-red-600 text-white');
                    btnAdd.className = btnAdd.className.replace(/border-teal-600 bg-teal-600 text-white/,
                        'border-slate-200 bg-white text-slate-400');
                }
                refreshModalPreview(pid, currentStock);
            }

            function changeModalQty(pid, currentStock, delta) {
                const input = document.getElementById('mQty' + pid);
                const next = Math.max(1, (parseInt(input.value, 10) || 1) + delta);
                input.value = next;
                refreshModalPreview(pid, currentStock);
            }

            function setModalQty(pid, currentStock, val) {
                document.getElementById('mQty' + pid).value = val;
                refreshModalPreview(pid, currentStock);
            }

            function refreshModalPreview(pid, currentStock) {
                const type = _modalType[pid] || 'add';
                const qty = Math.max(1, parseInt(document.getElementById('mQty' + pid).value, 10) || 1);
                const signed = type === 'add' ? qty : -qty;
                const result = currentStock + signed;
                const preview = document.getElementById('mPreview' + pid);
                const adjHid = document.getElementById('modalAdj' + pid);

                adjHid.value = signed;
                preview.textContent = result;
                preview.className = preview.className.replace(/text-teal-600|text-red-600|text-amber-500/, '');
                if (result < 0) preview.classList.add('text-red-600');
                else if (result === 0) preview.classList.add('text-amber-500');
                else preview.classList.add('text-teal-600');
            }

            function validateStockModal(pid, currentStock) {
                const type = _modalType[pid] || 'add';
                const qty = Math.max(1, parseInt(document.getElementById('mQty' + pid).value, 10) || 1);
                const signed = type === 'add' ? qty : -qty;
                if (currentStock + signed < 0) {
                    alert('Stok tidak boleh menjadi negatif!');
                    return false;
                }
                return true;
            }

            // Init default type for all modals
            document.querySelectorAll('[id^="stockModal"]').forEach(function(modal) {
                const pid = modal.id.replace('stockModal', '');
                _modalType[pid] = 'add';
            });
        </script>
    @endpush

@endsection
