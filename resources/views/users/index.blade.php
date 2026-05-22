@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h2 class="text-xl font-bold text-slate-900 mb-1">Daftar Pengguna</h2>
    <p class="text-sm text-slate-500">Total {{ $users->total() }} pengguna terdaftar</p>
  </div>
  <a href="{{ route('users.create') }}" class="btn-amber">
    <i class="bi bi-person-plus-fill"></i> Tambah User
  </a>
</div>

<div class="mb-4">
  <x-search-bar
    action="{{ route('users.index') }}"
    placeholder="Cari nama atau email..."
    :filters="[
      ['name' => 'role', 'label' => 'Semua Role', 'type' => 'select',
       'options' => $roles->pluck('name', 'name')->map(fn($n) => ucfirst($n))->toArray()],
      ['name' => 'is_active', 'label' => 'Semua Status', 'type' => 'select',
       'options' => ['1' => 'Aktif', '0' => 'Nonaktif']],
    ]"
  />
</div>

<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr>
          <th>Pengguna</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Bergabung</th>
          <th class="text-right" style="width:100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($users as $user)
        <tr class="group">
          <td>
            <div class="flex items-center gap-3">
              {{-- Avatar with design-system gradient --}}
              <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                   style="background: linear-gradient(135deg, #0D9488, #14B8A6);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
              </div>
              <div>
                <div class="text-sm font-semibold text-slate-900">{{ $user->name }}</div>
                @if ($user->id === auth()->id())
                  <div class="text-[10px] font-semibold text-teal-600">● Anda</div>
                @endif
              </div>
            </div>
          </td>
          <td class="text-slate-600 text-sm">{{ $user->email }}</td>
          <td>
            @foreach ($user->roles as $role)
              <span class="badge-role badge-{{ $role->name }}">{{ ucfirst($role->name) }}</span>
            @endforeach
          </td>
          <td>
            @if ($user->is_active)
              <span class="badge-role badge-active">Aktif</span>
            @else
              <span class="badge-role badge-inactive">Nonaktif</span>
            @endif
          </td>
          <td class="text-xs text-slate-500 font-mono">{{ $user->created_at->format('d M Y') }}</td>
          <td>
            <div class="flex items-center justify-end gap-1.5">
              <a href="{{ route('users.edit', $user) }}" class="topbar-btn" title="Edit">
                <i class="bi bi-pencil text-xs"></i>
              </a>
              @if ($user->id !== auth()->id())
              <form method="POST" action="{{ route('users.destroy', $user) }}"
                onsubmit="return confirm('Hapus user {{ addslashes($user->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="topbar-btn hover:!text-red-600 hover:!bg-red-50 hover:!border-red-200" title="Hapus">
                  <i class="bi bi-trash text-xs"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="py-14 text-center text-slate-400">
              <i class="bi bi-people block text-4xl mb-3 text-slate-300"></i>
              <div class="text-sm font-medium">
                @if (request('search'))
                  Tidak ada user yang cocok dengan "<strong class="text-slate-600">{{ request('search') }}</strong>"
                @else
                  Tidak ada pengguna ditemukan
                @endif
              </div>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($users->hasPages())
  <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
    {{ $users->links('vendor.pagination.custom') }}
  </div>
  @endif
</div>

@endsection