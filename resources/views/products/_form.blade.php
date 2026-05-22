{{-- resources/views/products/_form.blade.php --}}
{{-- Dipakai oleh create.blade.php dan edit.blade.php --}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

  {{-- Kolom kiri --}}
  <div class="lg:col-span-2">

    <div class="card p-4">
      <h6 class="font-semibold mb-3" style="font-size:14px;">Informasi Produk</h6>

      <div class="mb-3">
        <label class="form-label" style="font-size:13px; font-weight:500;">Nama Produk <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
          class="form-control @error('name') is-invalid @enderror"
          style="font-size:13.5px;" placeholder="Masukkan nama produk">
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">SKU <span class="text-red-500">*</span></label>
          <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
            class="form-control @error('sku') is-invalid @enderror"
            style="font-size:13.5px; font-family:'DM Mono',monospace;" placeholder="Contoh: PRD-001"
            autofocus onkeydown="if(event.key === 'Enter') { event.preventDefault(); document.querySelector('input[name=name]').focus(); }">
          @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Kategori <span class="text-red-500">*</span></label>
          <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" style="font-size:13.5px;">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
              </option>
            @endforeach
          </select>
          @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Satuan <span class="text-red-500">*</span></label>
          <select name="unit" class="form-select @error('unit') is-invalid @enderror" style="font-size:13.5px;">
            @foreach (['pcs' => 'Pcs (Satuan)', 'kg' => 'Kilogram (kg)', 'gram' => 'Gram (g)', 'liter' => 'Liter (L)', 'meter' => 'Meter (m)', 'pack' => 'Pack', 'box' => 'Box', 'lusin' => 'Lusin (12 pcs)'] as $val => $label)
              <option value="{{ $val }}" {{ old('unit', $product->unit ?? 'pcs') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
          @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-3">
        <label class="form-label" style="font-size:13px; font-weight:500;">Deskripsi</label>
        <textarea name="description" rows="3"
          class="form-control @error('description') is-invalid @enderror"
          style="font-size:13.5px;" placeholder="Deskripsi produk (opsional)">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
    </div>

    <div class="card p-4 mt-3">
      <h6 class="font-semibold mb-3" style="font-size:14px;">Harga & Stok</h6>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Harga Jual <span class="text-red-500">*</span></label>
          <div class="flex">
            <span class="flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-white text-sm text-gray-500">Rp</span>
            <input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price ?? '') }}"
              class="form-control @error('sell_price') is-invalid @enderror"
              style="font-size:13.5px;" placeholder="0" min="0" step="100">
            @error('sell_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Harga Beli <span class="text-red-500">*</span></label>
          <div class="flex">
            <span class="flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-white text-sm text-gray-500">Rp</span>
            <input type="number" name="buy_price" value="{{ old('buy_price', $product->buy_price ?? '') }}"
              class="form-control @error('buy_price') is-invalid @enderror"
              style="font-size:13.5px;" placeholder="0" min="0" step="100">
            @error('buy_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Harga Grosir</label>
          <div class="flex">
            <span class="flex items-center px-3 border border-r-0 border-slate-200 rounded-l-lg bg-white text-sm text-gray-500">Rp</span>
            <input type="number" name="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price ?? '') }}"
              class="form-control @error('wholesale_price') is-invalid @enderror"
              style="font-size:13.5px;" placeholder="Opsional" min="0" step="100">
            @error('wholesale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Min Qty Grosir</label>
          <input type="number" name="wholesale_min_qty" value="{{ old('wholesale_min_qty', $product->wholesale_min_qty ?? '') }}"
            class="form-control @error('wholesale_min_qty') is-invalid @enderror"
            style="font-size:13.5px;" placeholder="Opsional" min="0" step="0.001">
          @error('wholesale_min_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        @if (!$product)
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Stok Awal</label>
          <input type="number" name="stock_qty" value="{{ old('stock_qty', 0) }}"
            class="form-control @error('stock_qty') is-invalid @enderror"
            style="font-size:13.5px;" min="0" step="0.001">
          @error('stock_qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        @endif
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Stok Minimum</label>
          <input type="number" name="stock_min" value="{{ old('stock_min', $product->stock_min ?? 5) }}"
            class="form-control @error('stock_min') is-invalid @enderror"
            style="font-size:13.5px;" min="0" step="0.001">
          <div class="form-text" style="font-size:11px;">Alert akan muncul jika stok ≤ nilai ini</div>
          @error('stock_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      {{-- ── Panel Update Stok (hanya mode edit) ─────────── --}}
      @if ($product)
      @can('product.edit')
      <div style="margin-top:18px; padding-top:16px; border-top:1.5px dashed #E2E8F0;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
          <span style="font-size:13px; font-weight:600; color:#0F172A;">Update Stok</span>
          <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:12px; color:#64748B;">Stok saat ini:</span>
            <span id="currentStockBadge"
              style="font-size:13px; font-weight:700; padding:2px 10px; border-radius:99px; font-family:'DM Mono',monospace;
                     color:{{ $product->is_low_stock ? '#DC2626' : '#0D9488' }};
                     background:{{ $product->is_low_stock ? '#FEE2E2' : '#CCFBF1' }};">
              {{ $product->stock_qty }}
            </span>
          </div>
        </div>

        <form method="POST" action="{{ route('products.adjust-stock', $product) }}" id="adjustStockForm"
          style="background:#F8FAFC; border:1.5px solid #E2E8F0; border-radius:12px; padding:14px 16px;">
          @csrf

          {{-- Toggle: Tambah / Kurangi --}}
          <div style="display:flex; gap:8px; margin-bottom:12px;">
            <button type="button" id="btnAdd" onclick="setAdjustType('add')"
              style="flex:1; padding:8px; border-radius:8px; border:2px solid #0D9488; background:#0D9488;
                     color:white; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.15s;
                     display:flex; align-items:center; justify-content:center; gap:6px;">
              <i class="bi bi-plus-lg"></i> Tambah Stok
            </button>
            <button type="button" id="btnReduce" onclick="setAdjustType('reduce')"
              style="flex:1; padding:8px; border-radius:8px; border:2px solid #E2E8F0; background:white;
                     color:#64748B; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.15s;
                     display:flex; align-items:center; justify-content:center; gap:6px;">
              <i class="bi bi-dash-lg"></i> Kurangi Stok
            </button>
          </div>

          {{-- Input jumlah --}}
          <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px; flex-wrap:wrap;">
            <button type="button" onclick="changeQtyStep(-1)"
              style="width:38px; height:38px; border-radius:8px; border:1.5px solid #E2E8F0; background:white;
                     font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center;
                     color:#64748B; transition:all 0.12s; flex-shrink:0; line-height:1;"
              onmouseover="this.style.borderColor='#0D9488';this.style.color='#0D9488'"
              onmouseout="this.style.borderColor='#E2E8F0';this.style.color='#64748B'">&#8722;</button>

            <input type="number" id="adjustQtyInput" value="1" min="0.001" step="0.001"
              style="width:90px; padding:8px; border:2px solid #0D9488; border-radius:8px;
                     font-size:20px; font-weight:700; text-align:center; font-family:'DM Mono',monospace;
                     background:white; outline:none; flex-shrink:0;"
              oninput="onQtyChange()">
            <input type="hidden" name="adjustment" id="adjustmentHidden" value="1">

            <button type="button" onclick="changeQtyStep(1)"
              style="width:38px; height:38px; border-radius:8px; border:1.5px solid #E2E8F0; background:white;
                     font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center;
                     color:#64748B; transition:all 0.12s; flex-shrink:0; line-height:1;"
              onmouseover="this.style.borderColor='#0D9488';this.style.color='#0D9488'"
              onmouseout="this.style.borderColor='#E2E8F0';this.style.color='#64748B'">+</button>

            {{-- Quick preset --}}
            <div style="display:flex; gap:4px; flex-wrap:wrap;">
              @foreach ([5, 10, 25] as $preset)
              <button type="button" onclick="setQty({{ $preset }})"
                style="padding:5px 10px; border-radius:6px; border:1.5px solid #E2E8F0; background:white;
                       font-size:11px; font-weight:600; color:#64748B; cursor:pointer; transition:all 0.12s;"
                onmouseover="this.style.borderColor='#0D9488';this.style.color='#0D9488'"
                onmouseout="this.style.borderColor='#E2E8F0';this.style.color='#64748B'">+{{ $preset }}</button>
              @endforeach
            </div>
          </div>

          {{-- Preview hasil --}}
          <div style="display:flex; align-items:center; justify-content:space-between; background:white;
                      border:1px solid #E2E8F0; border-radius:8px; padding:9px 14px; margin-bottom:10px;">
            <span style="font-size:12px; color:#64748B;">Stok setelah update:</span>
            <span id="previewResult"
              style="font-size:15px; font-weight:700; font-family:'DM Mono',monospace; color:#0D9488; transition:color 0.15s;">
              {{ $product->stock_qty + 1 }}
            </span>
          </div>

          {{-- Catatan --}}
          <input type="text" name="notes" placeholder="Catatan (opsional)" maxlength="255"
            style="width:100%; padding:9px 12px; border:1.5px solid #E2E8F0; border-radius:8px;
                   font-size:12.5px; font-family:inherit; background:white; outline:none;
                   margin-bottom:10px; color:#0F172A; box-sizing:border-box;"
            onfocus="this.style.borderColor='#0D9488'" onblur="this.style.borderColor='#E2E8F0'">

          {{-- Submit --}}
          <button type="submit"
            style="width:100%; padding:11px; border-radius:8px; border:none; background:#0F172A;
                   color:white; font-size:13px; font-weight:600; cursor:pointer; transition:background 0.15s;
                   display:flex; align-items:center; justify-content:center; gap:7px;"
            onmouseover="this.style.background='#1E293B'" onmouseout="this.style.background='#0F172A'">
            <i class="bi bi-check-circle"></i>
            <span id="adjustSubmitLabel">Simpan Penambahan Stok</span>
          </button>
        </form>
      </div>
      @endcan
      @endif

    </div>

  </div>

  {{-- Kolom kanan --}}
  <div>

    <div class="card p-4">
      <h6 class="font-semibold mb-3" style="font-size:14px;">Gambar Produk</h6>

      {{-- Preview --}}
      <div id="imagePreview" style="width:100%; aspect-ratio:1; border-radius:10px; border:2px dashed var(--border); background:var(--body-bg); display:flex; align-items:center; justify-content:center; margin-bottom:12px; overflow:hidden; cursor:pointer;" onclick="document.getElementById('imageInput').click()">
        @if ($product && $product->image)
          <img src="{{ asset('storage/' . $product->image) }}" id="previewImg" style="width:100%; height:100%; object-fit:cover;">
        @else
          <div id="previewPlaceholder" style="text-align:center; color:#64748B;">
            <i class="bi bi-image" style="font-size:32px; opacity:0.4; display:block; margin-bottom:6px;"></i>
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
      @error('image') <div class="text-red-600 mt-1" style="font-size:12px;">{{ $message }}</div> @enderror
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
      <a href="{{ route('products.index') }}" class="btn-outline-sm w-full text-center" style="font-size:13.5px; padding:10px;">
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
      preview.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}

// ── Stock Adjustment Panel ─────────────────────────────────────
// Only runs if the panel exists (edit mode only)
(function () {
  const qtyInput    = document.getElementById('adjustQtyInput');
  const hiddenInput = document.getElementById('adjustmentHidden');
  const preview     = document.getElementById('previewResult');
  const label       = document.getElementById('adjustSubmitLabel');
  const btnAdd      = document.getElementById('btnAdd');
  const btnReduce   = document.getElementById('btnReduce');

  if (!qtyInput) return; // create mode — skip

  // Current stock from the badge (set server-side, supports decimal)
  const currentStock = parseFloat(
    document.getElementById('currentStockBadge').textContent.trim()
  );

  let adjustType = 'add'; // 'add' | 'reduce'

  // Styles for active / inactive toggle buttons
  const ACTIVE_ADD    = 'flex:1;padding:8px;border-radius:8px;border:2px solid #0D9488;background:#0D9488;color:white;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;display:flex;align-items:center;justify-content:center;gap:6px;';
  const INACTIVE_ADD  = 'flex:1;padding:8px;border-radius:8px;border:2px solid #E2E8F0;background:white;color:#64748B;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;display:flex;align-items:center;justify-content:center;gap:6px;';
  const ACTIVE_RED    = 'flex:1;padding:8px;border-radius:8px;border:2px solid #DC2626;background:#DC2626;color:white;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;display:flex;align-items:center;justify-content:center;gap:6px;';
  const INACTIVE_RED  = 'flex:1;padding:8px;border-radius:8px;border:2px solid #E2E8F0;background:white;color:#64748B;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.15s;display:flex;align-items:center;justify-content:center;gap:6px;';

  window.setAdjustType = function (type) {
    adjustType = type;
    if (type === 'add') {
      btnAdd.style.cssText    = ACTIVE_ADD;
      btnReduce.style.cssText = INACTIVE_RED;
    } else {
      btnAdd.style.cssText    = INACTIVE_ADD;
      btnReduce.style.cssText = ACTIVE_RED;
    }
    updatePreview();
  };

  window.changeQtyStep = function (delta) {
    const current = parseFloat(qtyInput.value) || 1;
    const next    = Math.max(0.001, +(current + delta).toFixed(3));
    qtyInput.value = next;
    updatePreview();
  };

  window.setQty = function (val) {
    qtyInput.value = val;
    updatePreview();
  };

  window.onQtyChange = function () {
    const v = parseFloat(qtyInput.value);
    if (isNaN(v) || v <= 0) qtyInput.value = 1;
    updatePreview();
  };

  function updatePreview() {
    const qty = Math.max(0.001, parseFloat(qtyInput.value) || 1);
    const signed = adjustType === 'add' ? qty : -qty;

    hiddenInput.value = signed;

    const resultQty = +(currentStock + signed).toFixed(3);
    preview.textContent = resultQty % 1 === 0 ? resultQty : resultQty.toFixed(3);

    if (resultQty < 0) {
      preview.style.color = '#DC2626';
      label.textContent   = '⚠ Stok tidak boleh negatif';
    } else if (adjustType === 'add') {
      preview.style.color = '#0D9488';
      label.textContent   = 'Simpan Penambahan Stok';
    } else {
      preview.style.color = resultQty === 0 ? '#F59E0B' : '#DC2626';
      label.textContent   = 'Simpan Pengurangan Stok';
    }
  }

  // Prevent submit if result would be negative
  document.getElementById('adjustStockForm').addEventListener('submit', function (e) {
    const qty    = Math.max(0.001, parseFloat(qtyInput.value) || 1);
    const signed = adjustType === 'add' ? qty : -qty;
    if (currentStock + signed < 0) {
      e.preventDefault();
      preview.style.color = '#DC2626';
      label.textContent   = '⚠ Stok tidak boleh negatif!';
    }
  });

  // Init
  updatePreview();
})();
</script>
@endpush