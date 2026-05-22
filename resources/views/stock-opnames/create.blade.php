@extends('layouts.app')
@section('title', 'Buat Stok Opname')
@section('page-title', 'Stok Opname')

@section('content')

<div class="mb-4">
  <h5 class="font-semibold mb-1" style="font-size:17px;">Buat Stok Opname Baru</h5>
  <p class="mb-0 text-sm text-slate-500">
    Pilih produk dan masukkan jumlah stok fisik yang dihitung.
  </p>
</div>

@if (session('error'))
<div class="alert alert-danger" style="font-size:13px;">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('stock-opnames.store') }}" id="opnameForm">
  @csrf

  <div class="card mb-3">
    <div class="card-header-custom flex items-center justify-between">
      <span class="card-title-sm">Catatan Opname</span>
    </div>
    <div class="p-3">
      <textarea name="notes" class="form-control" rows="2" placeholder="Catatan umum (opsional)" style="font-size:13px;">{{ old('notes') }}</textarea>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header-custom flex items-center justify-between">
      <span class="card-title-sm">Pilih Produk</span>
      <div style="display:flex; gap:8px; align-items:center;">
        <input type="text" id="productSearch" placeholder="Cari produk..." class="form-control form-control-sm" style="width:220px; font-size:12px;">
        <button type="button" class="btn-amber" style="font-size:12px; padding:5px 12px;" onclick="addAllProducts()">
          <i class="bi bi-plus-circle"></i> Tambah Semua
        </button>
      </div>
    </div>

    <div class="p-3">
      <div id="productSelector" style="max-height:200px; overflow-y:auto; border:1px solid var(--border); border-radius:8px; margin-bottom:12px;">
        @foreach ($products as $product)
        <div class="product-select-row" data-name="{{ strtolower($product->name) }}" data-sku="{{ strtolower($product->sku) }}"
          style="display:flex; align-items:center; gap:10px; padding:8px 12px; border-bottom:1px solid var(--border); font-size:13px; cursor:pointer;"
          onclick="addProduct({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->sku }}', {{ $product->stock_qty }})">
          <i class="bi bi-plus-circle" style="color:#0D9488; flex-shrink:0;"></i>
          <span style="flex:1; font-weight:500;">{{ $product->name }}</span>
          <span style="font-family:'DM Mono',monospace; font-size:11px; color:#64748B;">{{ $product->sku }}</span>
          <span style="font-size:12px; color:#64748B;">Stok: {{ $product->stock_qty }}</span>
        </div>
        @endforeach
      </div>

      <div id="opnameItems">
        <p id="emptyMsg" style="text-align:center; color:#64748B; font-size:13px; padding:20px 0;">
          Klik produk di atas untuk menambahkan ke opname
        </p>
      </div>
    </div>
  </div>

  <div class="flex gap-2">
    <button type="submit" class="btn-amber" id="submitBtn" style="opacity:0.5; pointer-events:none;">
      <i class="bi bi-save"></i> Simpan Draft
    </button>
    <a href="{{ route('stock-opnames.index') }}" class="btn-outline-sm" style="padding:8px 16px;">Batal</a>
  </div>
</form>

@endsection

@push('scripts')
<script>
const addedProducts = {};
const productsData = {!! $productsJson !!};

document.getElementById('productSearch').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.product-select-row').forEach(function(row) {
    const match = row.dataset.name.includes(q) || row.dataset.sku.includes(q);
    row.style.display = match ? '' : 'none';
  });
});

function addProduct(id, name, sku, systemQty) {
  if (addedProducts[id]) return;
  addedProducts[id] = true;

  document.getElementById('emptyMsg').style.display = 'none';
  updateSubmitBtn();

  const idx = Object.keys(addedProducts).length - 1;
  const row = document.createElement('div');
  row.id = 'opname-row-' + id;
  row.style.cssText = 'display:flex; align-items:center; gap:10px; padding:10px 12px; background:var(--body-bg); border-radius:8px; margin-bottom:6px;';
  row.innerHTML =
    '<input type="hidden" name="items[' + idx + '][product_id]" value="' + id + '">' +
    '<div style="flex:1; min-width:0;">' +
      '<div style="font-size:13px; font-weight:500;">' + name + '</div>' +
      '<div style="font-size:11px; color:#64748B;">' + sku + ' &middot; Stok sistem: <strong>' + systemQty + '</strong></div>' +
    '</div>' +
    '<div style="display:flex; align-items:center; gap:6px;">' +
      '<label style="font-size:11px; color:#64748B; flex-shrink:0;">Stok fisik:</label>' +
      '<input type="number" name="items[' + idx + '][actual_qty]" value="' + systemQty + '" min="0" required ' +
        'style="width:80px; padding:6px 8px; border:1.5px solid var(--border); border-radius:6px; font-size:13px; font-weight:600; text-align:center;" ' +
        'oninput="highlightDiff(this,' + systemQty + ')">' +
    '</div>' +
    '<input type="text" name="items[' + idx + '][notes]" placeholder="Catatan" style="width:120px; padding:6px 8px; border:1px solid var(--border); border-radius:6px; font-size:11px;">' +
    '<button type="button" onclick="removeProduct(' + id + ')" style="background:none; border:none; color:#DC2626; cursor:pointer; font-size:16px;">' +
      '<i class="bi bi-x-circle"></i>' +
    '</button>';

  document.getElementById('opnameItems').appendChild(row);
}

function removeProduct(id) {
  delete addedProducts[id];
  const row = document.getElementById('opname-row-' + id);
  if (row) row.remove();
  if (Object.keys(addedProducts).length === 0) {
    document.getElementById('emptyMsg').style.display = '';
  }
  updateSubmitBtn();
}

function addAllProducts() {
  productsData.forEach(function(p) {
    addProduct(p.id, p.name, p.sku, p.stock_qty);
  });
}

function highlightDiff(input, systemQty) {
  const actual = parseInt(input.value) || 0;
  input.style.borderColor = actual !== systemQty ? '#DC2626' : 'var(--border)';
  input.style.color = actual !== systemQty ? '#DC2626' : 'var(--text-main)';
}

function updateSubmitBtn() {
  const btn = document.getElementById('submitBtn');
  const hasItems = Object.keys(addedProducts).length > 0;
  btn.style.opacity = hasItems ? '1' : '0.5';
  btn.style.pointerEvents = hasItems ? 'auto' : 'none';
}
</script>
@endpush
