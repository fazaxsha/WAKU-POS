@extends('layouts.app')
@section('title', 'Detail PO — ' . ($purchase->reference_no ?? 'PO-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT)))
@section('page-title', 'Detail Purchase Order')

@push('styles')
<style>
  .po-status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 14px; border-radius: 99px;
    font-size: 12px; font-weight: 700; font-family: 'DM Mono', monospace;
    letter-spacing: 0.04em; text-transform: uppercase;
  }
  .po-status-pending   { background: #EFF6FF; color: #2563EB; }
  .po-status-received  { background: #F0FDF4; color: #16A34A; }
  .po-status-cancelled { background: #FEF2F2; color: #DC2626; }

  .info-row {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 0; border-bottom: 1px solid #F8FAFC;
  }
  .info-row:last-child { border-bottom: none; padding-bottom: 0; }
  .info-label { font-size: 11.5px; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.05em; width: 130px; flex-shrink: 0; padding-top: 2px; }
  .info-value { font-size: 13.5px; color: #1E293B; font-weight: 500; flex: 1; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div class="flex items-center gap-3">
    <a href="{{ route('purchases.index') }}" class="topbar-btn" title="Kembali ke daftar PO">
      <i class="bi bi-arrow-left text-sm"></i>
    </a>
    <div>
      <div class="flex items-center gap-3 mb-0.5">
        <h2 class="text-xl font-bold text-slate-900">
          {{ $purchase->reference_no ?? 'PO-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
        </h2>
        @php
          $statusPillClass = match($purchase->status) {
            'received'  => 'po-status-received',
            'cancelled' => 'po-status-cancelled',
            default     => 'po-status-pending',
          };
          $statusIcon = match($purchase->status) {
            'received'  => 'bi-check-circle-fill',
            'cancelled' => 'bi-x-circle-fill',
            default     => 'bi-clock-fill',
          };
          $statusLabel = match($purchase->status) {
            'received'  => 'Diterima',
            'cancelled' => 'Dibatalkan',
            default     => 'Pending',
          };
        @endphp
        <span class="po-status-pill {{ $statusPillClass }}">
          <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
        </span>
      </div>
      <p class="text-sm text-slate-500">
        Dibuat pada {{ $purchase->created_at->translatedFormat('d F Y, H:i') }}
      </p>
    </div>
  </div>

  {{-- Action Buttons --}}
  <div class="flex flex-wrap items-center gap-2">
    @if ($purchase->status === 'pending')
      @can('purchase.create')
      <a href="{{ route('purchases.edit', $purchase) }}" class="btn-outline-sm h-9 px-4">
        <i class="bi bi-pencil"></i> Edit PO
      </a>
      @endcan
      @can('purchase.confirm')
      <form method="POST" action="{{ route('purchases.confirm', $purchase) }}"
        onsubmit="return confirm('Konfirmasi penerimaan barang ini?\n\nStok semua produk dalam PO ini akan diperbarui secara otomatis.')">
        @csrf @method('PATCH')
        <button type="submit"
          class="h-9 px-4 inline-flex items-center gap-2 text-sm font-semibold text-white
                 bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-colors">
          <i class="bi bi-check-circle-fill"></i> Konfirmasi Terima Barang
        </button>
      </form>
      @endcan
    @endif
  </div>
</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

  {{-- Left: PO Details --}}
  <div class="lg:col-span-1 space-y-5">

    {{-- PO Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#F0FDFA;color:#0F766E;">
          <i class="bi bi-file-text"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Informasi PO</span>
      </div>
      <div class="px-6 py-4">
        <div class="info-row">
          <span class="info-label">No. Referensi</span>
          <span class="info-value font-mono text-teal-700 text-sm bg-teal-50 px-2 py-0.5 rounded-md">
            {{ $purchase->reference_no ?? 'PO-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}
          </span>
        </div>
        <div class="info-row">
          <span class="info-label">Tanggal PO</span>
          <span class="info-value">{{ $purchase->purchase_date->translatedFormat('d F Y') }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Status</span>
          <span class="po-status-pill {{ $statusPillClass }}">
            <i class="bi {{ $statusIcon }}"></i> {{ $statusLabel }}
          </span>
        </div>
        <div class="info-row">
          <span class="info-label">Dibuat Oleh</span>
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                 style="background:linear-gradient(135deg,#0D9488,#14B8A6);">
              {{ strtoupper(substr($purchase->user->name ?? 'U', 0, 1)) }}
            </div>
            <span class="info-value">{{ $purchase->user->name ?? '—' }}</span>
          </div>
        </div>
        @if ($purchase->notes)
        <div class="info-row">
          <span class="info-label">Catatan</span>
          <span class="info-value text-slate-500 italic">{{ $purchase->notes }}</span>
        </div>
        @endif
      </div>
    </div>

    {{-- Supplier Card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#F0FDF4;color:#16A34A;">
          <i class="bi bi-building"></i>
        </span>
        <span class="text-sm font-bold text-slate-900">Supplier</span>
      </div>
      <div class="px-6 py-5">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center text-lg font-bold text-white flex-shrink-0"
               style="background:linear-gradient(135deg,#22C55E,#16A34A);">
            {{ strtoupper(substr($purchase->supplier->name ?? 'S', 0, 1)) }}
          </div>
          <div>
            <div class="font-bold text-slate-900">{{ $purchase->supplier->name }}</div>
            @if ($purchase->supplier->phone)
              <div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                <i class="bi bi-telephone"></i> {{ $purchase->supplier->phone }}
              </div>
            @endif
          </div>
        </div>
        @if ($purchase->supplier->email)
        <div class="flex items-center gap-2 text-sm text-slate-500">
          <i class="bi bi-envelope text-slate-400"></i>
          <a href="mailto:{{ $purchase->supplier->email }}" class="hover:text-teal-600 transition-colors">
            {{ $purchase->supplier->email }}
          </a>
        </div>
        @endif
        @if ($purchase->supplier->address)
        <div class="flex items-start gap-2 text-sm text-slate-500 mt-2">
          <i class="bi bi-geo-alt text-slate-400 mt-0.5"></i>
          <span>{{ $purchase->supplier->address }}</span>
        </div>
        @endif
      </div>
    </div>

    {{-- Cost Summary Card --}}
    <div class="rounded-2xl p-5 relative overflow-hidden" style="background:linear-gradient(135deg,#0D9488,#0F766E);color:white;">
      <div class="absolute right-[-12px] top-[-12px] text-[72px] opacity-[0.08] leading-none pointer-events-none">
        <i class="bi bi-cash-stack"></i>
      </div>
      <div class="text-[11px] font-semibold uppercase tracking-wider opacity-75 mb-2">Total Nilai PO</div>
      <div class="text-3xl font-extrabold tracking-tight mb-1">
        Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}
      </div>
      <div class="text-[12px] opacity-60 flex items-center gap-1 mt-2">
        <i class="bi bi-box-seam"></i>
        {{ $purchase->items->sum('qty') }} unit · {{ $purchase->items->count() }} jenis produk
      </div>
    </div>

  </div>

  {{-- Right: Items Table --}}
  <div class="lg:col-span-2">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <span class="w-7 h-7 rounded-lg flex items-center justify-center text-xs" style="background:#FFFBEB;color:#D97706;">
            <i class="bi bi-cart-check"></i>
          </span>
          <span class="text-sm font-bold text-slate-900">Daftar Item</span>
        </div>
        <span class="text-xs font-mono text-slate-400">{{ $purchase->items->count() }} produk</span>
      </div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Produk</th>
              <th class="text-right">Qty</th>
              <th class="text-right">Harga Beli/unit</th>
              <th class="text-right">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($purchase->items as $i => $item)
            <tr>
              <td class="text-xs font-semibold text-slate-400">
                {{ $i + 1 }}
              </td>
              <td>
                <div class="flex items-center gap-3">
                  <div class="w-[38px] h-[38px] rounded-[10px] bg-slate-50 border border-slate-200 flex items-center justify-center text-lg flex-shrink-0 overflow-hidden">
                    @if ($item->product?->image)
                      <img src="{{ asset('storage/'.$item->product->image) }}" alt="" class="w-full h-full object-cover">
                    @else
                      📦
                    @endif
                  </div>
                  <div>
                    <div class="text-[13.5px] font-semibold text-slate-800">
                      {{ $item->product->name ?? '— Produk dihapus —' }}
                    </div>
                    <div class="text-[11px] text-slate-400 font-mono">
                      {{ $item->product->sku ?? '' }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="text-right font-bold text-slate-900">
                {{ $item->product?->unit === 'pcs' ? (int)$item->qty : rtrim(rtrim(number_format($item->qty, 3, ',', '.'), '0'), ',') }}
                <span class="text-[11px] text-slate-400 font-normal"> {{ $item->product?->unit ?? 'unit' }}</span>
              </td>
              <td class="text-right text-slate-500 text-[13px]">
                Rp {{ number_format($item->unit_cost, 0, ',', '.') }}
              </td>
              <td class="text-right">
                <span class="text-sm font-bold text-teal-600">
                  Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="bg-slate-50 border-t-2 border-slate-200">
              <td colspan="4" class="!py-4 text-right text-[13px] font-bold text-slate-500 uppercase tracking-wide">
                Total Nilai PO
              </td>
              <td class="!py-4 text-right">
                <span class="text-lg font-extrabold text-teal-700">
                  Rp {{ number_format($purchase->total_cost, 0, ',', '.') }}
                </span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- Confirmation CTA (if pending) --}}
      @if ($purchase->status === 'pending')
        @can('purchase.confirm')
        <div class="px-6 py-5 bg-amber-50 border-t border-amber-100">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
              <i class="bi bi-info-circle-fill text-amber-500 text-lg mt-0.5"></i>
              <div>
                <div class="text-sm font-semibold text-amber-800">PO Menunggu Konfirmasi</div>
                <div class="text-xs text-amber-600 mt-0.5">
                  Setelah dikonfirmasi, stok semua produk dalam PO ini akan bertambah secara otomatis.
                </div>
              </div>
            </div>
            <form method="POST" action="{{ route('purchases.confirm', $purchase) }}"
              onsubmit="return confirm('Konfirmasi penerimaan barang? Stok produk akan diperbarui otomatis.')">
              @csrf @method('PATCH')
              <button type="submit"
                class="whitespace-nowrap h-9 px-5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700
                       rounded-xl transition-colors shadow-sm inline-flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> Konfirmasi Terima
              </button>
            </form>
          </div>
        </div>
        @endcan
      @elseif ($purchase->status === 'received')
      <div class="px-6 py-4 bg-emerald-50 border-t border-emerald-100 flex items-center gap-3">
        <i class="bi bi-check-circle-fill text-emerald-500 text-lg"></i>
        <div>
          <div class="text-sm font-semibold text-emerald-800">PO Telah Diterima</div>
          <div class="text-xs text-emerald-600">Stok produk sudah diperbarui pada {{ $purchase->updated_at->translatedFormat('d F Y, H:i') }}.</div>
        </div>
      </div>
      @endif

    </div>
  </div>

</div>

@endsection
