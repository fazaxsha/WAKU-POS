{{-- ============================================================ --}}
{{-- resources/views/purchases/_form.blade.php                  --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2">

    {{-- Info PO --}}
    <div class="card p-4 mb-3">
      <h6 class="font-semibold mb-3" style="font-size:14px;">Informasi Order</h6>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Supplier <span class="text-red-500">*</span></label>
          <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" style="font-size:13.5px;">
            <option value="">-- Pilih Supplier --</option>
            @foreach ($suppliers as $s)
              <option value="{{ $s->id }}" {{ old('supplier_id', $purchase->supplier_id ?? '') == $s->id ? 'selected' : '' }}>
                {{ $s->name }}
              </option>
            @endforeach
          </select>
          @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Tanggal PO <span class="text-red-500">*</span></label>
          <input type="date" name="purchase_date"
            value="{{ old('purchase_date', isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
            class="form-control @error('purchase_date') is-invalid @enderror" style="font-size:13.5px;">
          @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">No. Referensi</label>
          <input type="text" name="reference_no"
            value="{{ old('reference_no', $purchase->reference_no ?? '') }}"
            class="form-control @error('reference_no') is-invalid @enderror"
            style="font-size:13.5px; font-family:'DM Mono',monospace;" placeholder="Opsional">
          @error('reference_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Catatan</label>
          <input type="text" name="notes" value="{{ old('notes', $purchase->notes ?? '') }}"
            class="form-control" style="font-size:13.5px;" placeholder="Opsional">
        </div>
      </div>
    </div>

    {{-- Item produk --}}
    <div class="card p-4">
      <div class="flex items-center justify-between mb-3">
        <h6 class="font-semibold mb-0" style="font-size:14px;">Item Pesanan</h6>
        <button type="button" class="btn-amber" style="padding:6px 12px; font-size:12px;" onclick="addRow()">
          <i class="bi bi-plus-lg mr-1"></i> Tambah Item
        </button>
      </div>

      <div class="table-responsive">
        <table class="table" id="itemsTable">
          <thead>
            <tr>
              <th>Produk</th>
              <th style="width:100px;">Qty</th>
              <th style="width:140px;">Harga Beli (Rp)</th>
              <th style="width:120px;">Subtotal</th>
              <th style="width:40px;"></th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            @if ($purchase && $purchase->items->count())
              @foreach ($purchase->items as $i => $item)
              <tr class="item-row">
                <td>
                  <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm product-select" style="font-size:13px;" onchange="updatePrice(this)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $p)
                      <option value="{{ $p->id }}"
                        data-price="{{ $p->buy_price }}"
                        {{ $item->product_id == $p->id ? 'selected' : '' }}>
                        {{ $p->name }} ({{ $p->sku }})
                      </option>
                    @endforeach
                  </select>
                </td>
                <td>
                  <input type="number" name="items[{{ $i }}][qty]" value="{{ $item->qty }}"
                    class="form-control form-control-sm qty-input" style="font-size:13px;" min="1" oninput="calcRow(this)">
                </td>
                <td>
                  <input type="number" name="items[{{ $i }}][unit_cost]" value="{{ $item->unit_cost }}"
                    class="form-control form-control-sm cost-input" style="font-size:13px;" min="0" oninput="calcRow(this)">
                </td>
                <td>
                  <span class="subtotal-display font-semibold" style="font-size:13px;">
                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                  </span>
                </td>
                <td>
                  <button type="button" class="btn btn-sm" style="color:#DC2626; background:none; border:none; padding:0;" onclick="removeRow(this)">
                    <i class="bi bi-x-circle"></i>
                  </button>
                </td>
              </tr>
              @endforeach
            @else
              {{-- Row kosong default --}}
              <tr class="item-row">
                <td>
                  <select name="items[0][product_id]" class="form-select form-select-sm product-select" style="font-size:13px;" onchange="updatePrice(this)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $p)
                      <option value="{{ $p->id }}" data-price="{{ $p->buy_price }}">
                        {{ $p->name }} ({{ $p->sku }})
                      </option>
                    @endforeach
                  </select>
                </td>
                <td><input type="number" name="items[0][qty]" value="1" class="form-control form-control-sm qty-input" style="font-size:13px;" min="1" oninput="calcRow(this)"></td>
                <td><input type="number" name="items[0][unit_cost]" value="" class="form-control form-control-sm cost-input" style="font-size:13px;" min="0" placeholder="0" oninput="calcRow(this)"></td>
                <td><span class="subtotal-display font-semibold" style="font-size:13px;">Rp 0</span></td>
                <td><button type="button" class="btn btn-sm" style="color:#DC2626; background:none; border:none; padding:0;" onclick="removeRow(this)"><i class="bi bi-x-circle"></i></button></td>
              </tr>
            @endif
          </tbody>
          <tfoot>
            <tr style="background:#FAFAF9;">
              <td colspan="3" class="text-end font-semibold" style="font-size:14px; padding:12px 14px;">Grand Total</td>
              <td style="padding:12px 14px;">
                <span id="grandTotal" class="font-bold" style="font-size:15px; color:#D97706;">Rp 0</span>
              </td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
      @error('items') <div class="text-red-600 mt-2" style="font-size:12px;">{{ $message }}</div> @enderror
    </div>

  </div>

  <div>
    <div class="card p-4 mb-3">
      <h6 class="font-semibold mb-3" style="font-size:14px;">Ringkasan</h6>
      <div class="flex justify-between mb-2" style="font-size:13px;">
        <span class="text-slate-500">Jumlah Item</span>
        <span id="summaryItems">0 item</span>
      </div>
      <div class="flex justify-between" style="font-size:14px; font-weight:600; padding-top:8px; border-top:1px solid var(--border);">
        <span>Total Biaya</span>
        <span id="summaryTotal" style="color:#D97706;">Rp 0</span>
      </div>
    </div>

    <div class="flex flex-col gap-2">
      <button type="submit" class="btn-amber w-full" style="padding:11px;">
        <i class="bi bi-check-lg mr-1"></i>
        {{ $purchase ? 'Simpan Perubahan' : 'Buat Purchase Order' }}
      </button>
      <a href="{{ route('purchases.index') }}" class="btn-outline-sm w-full text-center" style="font-size:13.5px; padding:10px;">Batal</a>
    </div>
  </div>
</div>

@push('scripts')
<script>
let rowIndex = {{ $purchase ? $purchase->items->count() : 1 }};

const productOptions = `
  <option value="">-- Pilih Produk --</option>
  @foreach ($products as $p)
    <option value="{{ $p->id }}" data-price="{{ $p->buy_price }}">{{ addslashes($p->name) }} ({{ $p->sku }})</option>
  @endforeach
`;

function addRow() {
  const tbody = document.getElementById('itemsBody');
  const tr    = document.createElement('tr');
  tr.className = 'item-row';
  tr.innerHTML = `
    <td><select name="items[${rowIndex}][product_id]" class="form-select form-select-sm product-select" style="font-size:13px;" onchange="updatePrice(this)">${productOptions}</select></td>
    <td><input type="number" name="items[${rowIndex}][qty]" value="1" class="form-control form-control-sm qty-input" style="font-size:13px;" min="1" oninput="calcRow(this)"></td>
    <td><input type="number" name="items[${rowIndex}][unit_cost]" value="" class="form-control form-control-sm cost-input" style="font-size:13px;" min="0" placeholder="0" oninput="calcRow(this)"></td>
    <td><span class="subtotal-display font-semibold" style="font-size:13px;">Rp 0</span></td>
    <td><button type="button" class="btn btn-sm" style="color:#DC2626; background:none; border:none; padding:0;" onclick="removeRow(this)"><i class="bi bi-x-circle"></i></button></td>
  `;
  tbody.appendChild(tr);
  rowIndex++;
  recalcAll();
}

function removeRow(btn) {
  const rows = document.querySelectorAll('.item-row');
  if (rows.length <= 1) { alert('Minimal harus ada 1 item.'); return; }
  btn.closest('tr').remove();
  reindexRows();
  recalcAll();
}

function updatePrice(select) {
  const opt  = select.options[select.selectedIndex];
  const price = opt.dataset.price || '';
  const row   = select.closest('tr');
  row.querySelector('.cost-input').value = price;
  calcRow(row.querySelector('.cost-input'));
}

function calcRow(input) {
  const row  = input.closest('tr');
  const qty  = parseFloat(row.querySelector('.qty-input').value) || 0;
  const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
  const sub  = qty * cost;
  row.querySelector('.subtotal-display').textContent = 'Rp ' + fmt(sub);
  recalcAll();
}

function recalcAll() {
  let total = 0;
  let count = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const qty  = parseFloat(row.querySelector('.qty-input').value) || 0;
    const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
    total += qty * cost;
    if (qty > 0 && cost > 0) count++;
  });
  document.getElementById('grandTotal').textContent   = 'Rp ' + fmt(total);
  document.getElementById('summaryTotal').textContent  = 'Rp ' + fmt(total);
  document.getElementById('summaryItems').textContent  = count + ' item';
}

function reindexRows() {
  document.querySelectorAll('.item-row').forEach((row, i) => {
    row.querySelector('[name*="product_id"]').name = `items[${i}][product_id]`;
    row.querySelector('[name*="qty"]').name         = `items[${i}][qty]`;
    row.querySelector('[name*="unit_cost"]').name   = `items[${i}][unit_cost]`;
  });
}

function fmt(n) { return Math.round(n).toLocaleString('id-ID'); }

// Init
recalcAll();
</script>
@endpush
