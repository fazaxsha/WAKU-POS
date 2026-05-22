@extends('layouts.app')
@section('title', 'Stok Opname')
@section('page-title', 'Stok Opname')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
  <div>
    <h5 class="font-semibold mb-1" style="font-size:17px;">Daftar Stok Opname</h5>
    <p class="mb-0 text-sm text-slate-500">
      Total {{ $opnames->total() }} stok opname
    </p>
  </div>
  <a href="{{ route('stock-opnames.create') }}" class="btn-amber no-underline">
    <i class="bi bi-plus-lg"></i> Buat Opname Baru
  </a>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>No. Opname</th>
          <th>Oleh</th>
          <th>Jumlah Item</th>
          <th>Selisih Total</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th style="width:100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($opnames as $opname)
        <tr>
          <td>
            <span style="font-family:'DM Mono',monospace; font-size:12px;">{{ $opname->opname_no }}</span>
          </td>
          <td style="font-size:13.5px;">{{ $opname->user->name }}</td>
          <td style="font-size:13.5px;">{{ $opname->items_count ?? $opname->items->count() }} produk</td>
          <td style="font-size:13.5px; font-weight:600;">
            @php $diff = $opname->items->sum('difference'); @endphp
            <span style="color: {{ $diff == 0 ? '#059669' : '#DC2626' }};">
              {{ $diff > 0 ? '+' : '' }}{{ $diff }}
            </span>
          </td>
          <td>
            @if ($opname->status === 'draft')
              <span class="badge-role badge-kasir">Draft</span>
            @elseif ($opname->status === 'confirmed')
              <span class="badge-role badge-owner">Dikonfirmasi</span>
            @else
              <span class="badge-role badge-admin">Dibatalkan</span>
            @endif
          </td>
          <td class="text-xs text-slate-500">{{ $opname->created_at->format('d M Y, H:i') }}</td>
          <td>
            <a href="{{ route('stock-opnames.show', $opname) }}" class="btn-outline-sm">
              <i class="bi bi-eye"></i> Detail
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-4 text-sm text-slate-500">
            Belum ada stok opname
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if ($opnames->hasPages())
<div class="mt-3">{{ $opnames->links('vendor.pagination.custom') }}</div>
@endif

@endsection
