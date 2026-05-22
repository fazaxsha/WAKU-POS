@extends('layouts.app')
@section('title', 'Detail Stok Opname')
@section('page-title', 'Stok Opname')

@section('content')

@if (session('success'))
<div class="alert alert-success" style="font-size:13px;">{{ session('success') }}</div>
@endif
@if (session('error'))
<div class="alert alert-danger" style="font-size:13px;">{{ session('error') }}</div>
@endif

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
  <div>
    <h5 class="font-semibold mb-1" style="font-size:17px;">
      {{ $stockOpname->opname_no }}
      @if ($stockOpname->status === 'draft')
        <span class="badge-role badge-kasir ml-2">Draft</span>
      @elseif ($stockOpname->status === 'confirmed')
        <span class="badge-role badge-owner ml-2">Dikonfirmasi</span>
      @else
        <span class="badge-role badge-admin ml-2">Dibatalkan</span>
      @endif
    </h5>
    <p class="mb-0 text-sm text-slate-500">
      Oleh {{ $stockOpname->user->name }} &middot; {{ $stockOpname->created_at->format('d M Y, H:i') }}
      @if ($stockOpname->confirmed_at)
        &middot; Dikonfirmasi: {{ $stockOpname->confirmed_at->format('d M Y, H:i') }}
      @endif
    </p>
  </div>

  @if ($stockOpname->status === 'draft')
  <div class="flex gap-2">
    <form method="POST" action="{{ route('stock-opnames.confirm', $stockOpname) }}" onsubmit="return confirm('Konfirmasi stok opname? Stok produk akan diperbarui sesuai hasil hitungan.')">
      @csrf @method('PATCH')
      <button class="btn-amber"><i class="bi bi-check-circle"></i> Konfirmasi & Update Stok</button>
    </form>
    <form method="POST" action="{{ route('stock-opnames.cancel', $stockOpname) }}" onsubmit="return confirm('Batalkan stok opname ini?')">
      @csrf @method('PATCH')
      <button class="btn-outline-sm" style="padding:8px 16px; color:#DC2626; border-color:#DC2626;">
        <i class="bi bi-x-circle"></i> Batalkan
      </button>
    </form>
  </div>
  @endif
</div>

@if ($stockOpname->notes)
<div class="card mb-3">
  <div class="p-3" style="font-size:13px;">
    <strong>Catatan:</strong> {{ $stockOpname->notes }}
  </div>
</div>
@endif

<div class="card">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Produk</th>
          <th>SKU</th>
          <th class="text-center">Stok Sistem</th>
          <th class="text-center">Stok Fisik</th>
          <th class="text-center">Selisih</th>
          <th>Catatan</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($stockOpname->items as $item)
        <tr>
          <td style="font-size:13.5px; font-weight:500;">{{ $item->product->name }}</td>
          <td><span style="font-family:'DM Mono',monospace; font-size:12px;">{{ $item->product->sku }}</span></td>
          <td class="text-center" style="font-size:13.5px;">{{ $item->product->unit === 'pcs' ? (int)$item->system_qty : rtrim(rtrim(number_format($item->system_qty, 3, ',', '.'), '0'), ',') }}</td>
          <td class="text-center" style="font-size:13.5px; font-weight:600;">{{ $item->product->unit === 'pcs' ? (int)$item->actual_qty : rtrim(rtrim(number_format($item->actual_qty, 3, ',', '.'), '0'), ',') }}</td>
          <td class="text-center" style="font-size:13.5px; font-weight:700;">
            @php $diff = $item->difference; @endphp
            <span style="color: {{ $diff == 0 ? '#059669' : ($diff > 0 ? '#2563EB' : '#DC2626') }};">
              {{ $diff > 0 ? '+' : '' }}{{ fmod($diff, 1) == 0 ? (int)$diff : rtrim(rtrim(number_format($diff, 3, ',', '.'), '0'), ',') }}
            </span>
          </td>
          <td class="text-xs text-slate-500">{{ $item->notes ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr style="border-top:2px solid var(--border);">
          <td colspan="4" class="text-end" style="font-weight:600; font-size:13px;">Total Selisih:</td>
          <td class="text-center" style="font-weight:700; font-size:14px;">
            @php $total = $stockOpname->items->sum('difference'); @endphp
            <span style="color: {{ $total == 0 ? '#059669' : '#DC2626' }};">
              {{ $total > 0 ? '+' : '' }}{{ fmod($total, 1) == 0 ? (int)$total : rtrim(rtrim(number_format($total, 3, ',', '.'), '0'), ',') }}
            </span>
          </td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
</div>

<div class="mt-3">
  <a href="{{ route('stock-opnames.index') }}" class="btn-outline-sm" style="padding:8px 16px;">
    <i class="bi bi-arrow-left"></i> Kembali
  </a>
</div>

@endsection
