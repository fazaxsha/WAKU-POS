{{-- resources/views/products/_form.blade.php --}}
{{-- Dipakai oleh create.blade.php dan edit.blade.php --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Kolom kiri --}}
    <div class="lg:col-span-2">

        <div class="card p-4">
            <h6 class="font-semibold mb-3" style="font-size:14px;">Informasi Produk</h6>

            <div class="mb-3">
                <label class="form-label" style="font-size:13px; font-weight:500;">Nama Produk <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
                    class="form-control @error('name') is-invalid @enderror" style="font-size:13.5px;"
                    placeholder="Masukkan nama produk">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="form-label" style="font-size:13px; font-weight:500;">SKU <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                        class="form-control @error('sku') is-invalid @enderror"
                        style="font-size:13.5px; font-family:'DM Mono',monospace;" placeholder="Contoh: PRD-001"
                        autofocus
                        onkeydown="if(event.key === 'Enter') { event.preventDefault(); document.querySelector('input[name=name]').focus(); }">
                    @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label" style="font-size:13px; font-weight:500;">Kategori <span
                            class="text-red-500">*</span></label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror"
                        style="font-size:13.5px;">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="form-label" style="font-size:13px; font-weight:500;">Satuan <span
                            class="text-red-500">*</span></label>
                    <select name="unit" class="form-select @error('unit') is-invalid @enderror"
                        style="font-size:13.5px;">
                        @foreach (['pcs' => 'Pcs (Satuan)', 'kg' => 'Kilogram (kg)', 'gram' => 'Gram (g)', 'liter' => 'Liter (L)', 'meter' => 'Meter (m)', 'pack' => 'Pack', 'box' => 'Box', 'lusin' => 'Lusin (12 pcs)'] as $val => $label)
                            <option value="{{ $val }}"
                                {{ old('unit', $product->unit ?? 'pcs') === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label" style="font-size:13px; font-weight:500;">Deskripsi</label>
                <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                    style="font-size:13.5px;" placeholder="Deskripsi produk (opsional)">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card p-4 mt-3">
            <h6 class="font-semibold mb-3" style="font-size:14px;">Harga & Stok</h6>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="form-label" style="font-size:13px; font-weight:500;">Harga Jual <span
                            class="text-red-500">*</span></label>
                    <div class="flex">
                        <span
                            class="flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-white text-sm text-gray-500">Rp</span>
                        <input type="number" name="sell_price"
                            value="{{ old('sell_price', $product->sell_price ?? '') }}"
                            class="form-control @error('sell_price') is-invalid @enderror" style="font-size:13.5px;"
                            placeholder="0" min="0" step="100">
                        @error('sell_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="form-label" style="font-size:13px; font-weight:500;">Harga Beli <span
                            class="text-red-500">*</span></label>
                    <div class="flex">
                        <span
                            class="flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-white text-sm text-gray-500">Rp</span>
                        <input type="number" name="buy_price"
                            value="{{ old('buy_price', $product->buy_price ?? '') }}"
                            class="form-control @error('buy_price') is-invalid @enderror" style="font-size:13.5px;"
                            placeholder="0" min="0" step="100">
                        @error('buy_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="form-label" style="font-size:13px; font-weight:500;">Harga Grosir</label>
                    <div class="flex">
                        <span
                            class="flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-white text-sm text-gray-500">Rp</span>
                        <input type="number" name="wholesale_price"
                            value="{{ old('wholesale_price', $product->wholesale_price ?? '') }}"
                            class="form-control @error('wholesale_price') is-invalid @enderror"
                            style="font-size:13.5px;" placeholder="Opsional" min="0" step="100">
                        @error('wholesale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="form-label" style="font-size:13px; font-weight:500;">Min Qty Grosir</label>
                    <input type="number" name="wholesale_min_qty"
                        value="{{ old('wholesale_min_qty', $product->wholesale_min_qty ?? '') }}"
                        class="form-control @error('wholesale_min_qty') is-invalid @enderror"
                        style="font-size:13.5px;" placeholder="Opsional" min="0" step="0.001">
                    @error('wholesale_min_qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>



        </div>

    </div>

    {{-- Kolom kanan --}}
    <div>

        <div class="card p-4">
            <h6 class="font-semibold mb-3" style="font-size:14px;">Gambar Produk</h6>

            {{-- Preview --}}
            <div id="imagePreview"
                style="width:100%; aspect-ratio:1; border-radius:10px; border:2px dashed var(--border); background:var(--body-bg); display:flex; align-items:center; justify-content:center; margin-bottom:12px; overflow:hidden; cursor:pointer;"
                onclick="document.getElementById('imageInput').click()">
                @if ($product && $product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" id="previewImg"
                        style="width:100%; height:100%; object-fit:cover;">
                @else
                    <div id="previewPlaceholder" style="text-align:center; color:#64748B;">
                        <i class="bi bi-image"
                            style="font-size:32px; opacity:0.4; display:block; margin-bottom:6px;"></i>
                        <span style="font-size:12px;">Klik untuk upload</span>
                    </div>
                @endif
            </div>

            <input type="file" name="image" id="imageInput" accept="image/*" class="hidden"
                onchange="previewImage(this)">
            <button type="button" class="btn-outline-sm w-full" style="font-size:12px;"
                onclick="document.getElementById('imageInput').click()">
                <i class="bi bi-upload mr-1"></i> Pilih Gambar
            </button>
            <div class="form-text mt-1" style="font-size:11px;">JPG, PNG, WebP. Maks 2MB.</div>
            @error('image')
                <div class="text-red-600 mt-1" style="font-size:12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="card p-4 mt-3">
            <h6 class="font-semibold mb-3" style="font-size:14px;">Status</h6>
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                    {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive" style="font-size:13px;">Produk Aktif</label>
            </div>
            <div class="form-text" style="font-size:11px;">Produk nonaktif tidak akan muncul di POS.</div>
        </div>

        <div class="mt-3 flex flex-col gap-2">
            <button type="submit" class="btn-amber w-full" style="padding:11px;">
                <i class="bi bi-check-lg mr-1"></i>
                {{ $product ? 'Simpan Perubahan' : 'Tambah Produk' }}
            </button>
            <a href="{{ route('products.index') }}" class="btn-outline-sm w-full text-center"
                style="font-size:13.5px; padding:10px;">
                Batal
            </a>
        </div>

    </div>

</div>

@push('scripts')
    <script>
        // ── Image preview ──────────────────────────────────────────────
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const preview = document.getElementById('imagePreview');
                    preview.innerHTML =
                        `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush
