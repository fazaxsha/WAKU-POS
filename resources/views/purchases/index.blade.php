@extends('layouts.app')
@section('title', 'Purchase Order')
@section('page-title', 'Purchase Order')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h2 class="text-xl font-bold text-slate-900 mb-1">Daftar Purchase Order</h2>
    <p class="text-sm text-slate-500">Total {{ $purchases->total() }} PO terdaftar</p>
  </div>
  @can('purchase.create')
  <a href="{{ route('purchases.create') }}" class="btn-amber">
    <i class="bi bi-plus-lg"></i> Buat PO Baru
  </a>
  @endcan
</div>

<div class="mb-4">
  <x-search-bar
    action="{{ route('purchases.index') }}"
    placeholder="Filter purchase order..."
    :filters="[
      ['name' => 'supplier_id', 'label' => 'Semua Supplier', 'type' => 'select',
       'options' => $suppliers->pluck('name', 'id')->toArray()],
      ['name' => 'status', 'label' => 'Semua Status', 'type' => 'select',
       'options' => ['pending' => 'Pending', 'received' => 'Diterima', 'cancelled' => 'Dibatalkan']],
      ['name' => 'from', 'label' => 'Dari Tanggal', 'type' => 'date'],
      ['name' => 'to',   'label' => 'Sampai Tanggal', 'type' => 'date'],
    ]"
    :per-page="true"
  />
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>No. Referensi</th>
          <th>Supplier</th>
          <th>Total Nilai</th>
          <th>Status</th>
          <th>Tanggal PO</th>
          <th>Dibuat Oleh</th>
          <th class="text-right" style="width:120px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($purchases as $purchase)
        <tr class="group">
          <td>
            <span class="font-mono text-xs text-teal-700 bg-teal-50 px-2 py-0.5 rounded-md">
              {{ $purchase->reference_no ?? 'PO-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
            </span>
          </td>
          <td class="font-semibold text-slate-900">{{ $purchase->supplier->name }}</td>
          <td class="font-bold text-slate-900">Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}</td>
          <td>
            @php
              $pillClass = match($purchase->status) {
                'received'  => 'background:#F0FDF4;color:#16A34A;',
                'cancelled' => 'background:#FEF2F2;color:#DC2626;',
                default     => 'background:#EFF6FF;color:#2563EB;',
              };
              $pillIcon = match($purchase->status) {
                'received'  => 'bi-check-circle-fill',
                'cancelled' => 'bi-x-circle',
                default     => 'bi-clock',
              };
              $pillLabel = match($purchase->status) {
                'received'  => 'Diterima',
                'cancelled' => 'Dibatalkan',
                default     => 'Pending',
              };
            @endphp
            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:10.5px;font-weight:700;font-family:'DM Mono',monospace;{{ $pillClass }}">
              <i class="bi {{ $pillIcon }}"></i> {{ $pillLabel }}
            </span>
          </td>
          <td class="text-xs text-slate-500 font-mono">{{ $purchase->purchase_date->format('d M Y') }}</td>
          <td class="text-sm text-slate-600">{{ $purchase->user->name ?? '—' }}</td>
          <td>
            <div class="flex items-center justify-end gap-1.5">
              <a href="{{ route('purchases.show', $purchase) }}" class="topbar-btn" title="Lihat Detail">
                <i class="bi bi-eye text-xs"></i>
              </a>
              @if ($purchase->status === 'pending')
                @can('purchase.confirm')
                <form method="POST" action="{{ route('purchases.confirm', $purchase) }}"
                  onsubmit="return confirm('Konfirmasi penerimaan barang? Stok akan diperbarui otomatis.')">
                  @csrf @method('PATCH')
                  <button type="submit" class="topbar-btn hover:!text-emerald-600 hover:!bg-emerald-50 hover:!border-emerald-200" title="Konfirmasi Terima">
                    <i class="bi bi-check-circle text-xs"></i>
                  </button>
                </form>
                @endcan
                @can('purchase.create')
                <a href="{{ route('purchases.edit', $purchase) }}" class="topbar-btn" title="Edit">
                  <i class="bi bi-pencil text-xs"></i>
                </a>
                @endcan
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <div class="py-14 text-center text-slate-400">
              <i class="bi bi-cart-plus block text-4xl mb-3 text-slate-300"></i>
              <div class="text-sm font-medium">
                @if (request()->anyFilled(['supplier_id', 'status', 'from', 'to']))
                  Tidak ada PO yang cocok dengan filter yang dipilih
                @else
                  Belum ada purchase order dibuat
                @endif
              </div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($purchases->hasPages())
  <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
    {{ $purchases->links('vendor.pagination.custom') }}
  </div>
  @endif
</div>

@endsection