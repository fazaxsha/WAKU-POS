{{-- resources/views/categories/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Manajemen Kategori')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h2 class="text-xl font-bold text-slate-900 mb-1">Daftar Kategori</h2>
    <p class="text-sm text-slate-500">Total {{ $categories->total() }} kategori terdaftar</p>
  </div>
  @can('product.create')
  <a href="{{ route('categories.create') }}" class="btn-amber">
    <i class="bi bi-plus-lg"></i> Tambah Kategori
  </a>
  @endcan
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>Nama Kategori</th>
          <th>Slug</th>
          <th>Deskripsi</th>
          <th>Jumlah Produk</th>
          <th>Status</th>
          <th class="text-right" style="width:100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($categories as $category)
        <tr class="group">
          <td class="font-semibold text-slate-900">{{ $category->name }}</td>
          <td>
            <span class="font-mono text-xs text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md">
              {{ $category->slug }}
            </span>
          </td>
          <td class="text-slate-500 text-sm">
            {{ Str::limit($category->description, 50) ?? '—' }}
          </td>
          <td>
            {{-- Correct semantic: neutral/slate badge for a count, not badge-admin --}}
            <span class="inline-flex items-center gap-1 text-[10px] font-semibold font-mono uppercase tracking-widest
                         bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full">
              {{ $category->products_count }} produk
            </span>
          </td>
          <td>
            @if ($category->is_active)
              <span class="badge-role badge-active">Aktif</span>
            @else
              <span class="badge-role badge-inactive">Nonaktif</span>
            @endif
          </td>
          <td>
            <div class="flex items-center justify-end gap-1.5">
              @can('product.edit')
              <a href="{{ route('categories.edit', $category) }}" class="topbar-btn" title="Edit">
                <i class="bi bi-pencil text-xs"></i>
              </a>
              @endcan
              @can('product.delete')
              <form method="POST" action="{{ route('categories.destroy', $category) }}"
                onsubmit="return confirm('Hapus kategori ini? Pastikan tidak ada produk yang menggunakannya.')">
                @csrf @method('DELETE')
                <button type="submit" class="topbar-btn hover:!text-red-600 hover:!bg-red-50 hover:!border-red-200" title="Hapus">
                  <i class="bi bi-trash text-xs"></i>
                </button>
              </form>
              @endcan
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="py-14 text-center text-slate-400">
              <i class="bi bi-tag block text-4xl mb-3 text-slate-300"></i>
              <div class="text-sm font-medium">Belum ada kategori ditemukan</div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($categories->hasPages())
  <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
    {{ $categories->links() }}
  </div>
  @endif
</div>

@endsection