@extends('layouts.app')
@section('title', 'Supplier')
@section('page-title', 'Manajemen Supplier')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
  <div>
    <h5 class="font-semibold mb-1" style="font-size:17px;">Daftar Supplier</h5>
    <p class="mb-0 text-sm text-slate-500">
      Total {{ $suppliers->total() }} supplier terdaftar
    </p>
  </div>
  @can('purchase.create')
  <a href="{{ route('suppliers.create') }}" class="btn-amber no-underline">
    <i class="bi bi-plus-lg"></i> Tambah Supplier
  </a>
  @endcan
</div>

<x-search-bar
  action="{{ route('suppliers.index') }}"
  placeholder="Cari nama, telepon, atau email..."
  :filters="[
    ['name' => 'is_active', 'label' => 'Semua Status', 'type' => 'select',
     'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
  ]"
/>

<div class="card">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Nama Supplier</th>
          <th>Telepon</th>
          <th>Email</th>
          <th>Alamat</th>
          <th>Total PO</th>
          <th>Status</th>
          <th style="width:100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($suppliers as $supplier)
        <tr>
          <td style="font-size:13.5px; font-weight:500;">{{ $supplier->name }}</td>
          <td style="font-size:13px;">{{ $supplier->phone ?? '-' }}</td>
          <td style="font-size:13px;">{{ $supplier->email ?? '-' }}</td>
          <td class="text-xs text-slate-500">
            {{ Str::limit($supplier->address, 40) ?? '-' }}
          </td>
          <td>
            <span class="badge-role badge-admin">{{ $supplier->purchases_count }} PO</span>
          </td>
          <td>
            <span class="badge-role {{ $supplier->is_active ? 'badge-active' : 'badge-inactive' }}">
              {{ $supplier->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </td>
          <td>
            <div class="flex gap-1">
              <a href="{{ route('suppliers.edit', $supplier) }}"
                class="topbar-btn no-underline" title="Edit"
                style="width:30px; height:30px; border-radius:6px; font-size:13px;">
                <i class="bi bi-pencil"></i>
              </a>
              <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}"
                onsubmit="return confirm('Hapus supplier ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="topbar-btn" title="Hapus"
                  style="width:30px; height:30px; border-radius:6px; font-size:13px; color:#DC2626;">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-5 text-sm text-slate-500">
            <i class="bi bi-truck block mb-2" style="font-size:28px; opacity:0.3;"></i>
            @if (request('search'))
              Tidak ada supplier cocok dengan "<strong>{{ request('search') }}</strong>"
            @else
              Belum ada supplier
            @endif
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if ($suppliers->hasPages() || $suppliers->total() > 0)
  <div class="px-4 py-3" style="border-top:1px solid var(--border);">
    {{ $suppliers->links('vendor.pagination.custom') }}
  </div>
  @endif
</div>

@endsection