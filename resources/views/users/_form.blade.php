{{-- ============================================================ --}}
{{-- resources/views/users/_form.blade.php                      --}}
{{-- ============================================================ --}}
<div class="mb-3">
  <label class="form-label" style="font-size:13px; font-weight:500;">Nama Lengkap <span class="text-red-500">*</span></label>
  <input type="text" name="name"
    value="{{ old('name', $user->name ?? '') }}"
    class="form-control @error('name') is-invalid @enderror"
    style="font-size:13.5px;" placeholder="Nama lengkap pengguna">
  @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
 
<div class="mb-3">
  <label class="form-label" style="font-size:13px; font-weight:500;">Email <span class="text-red-500">*</span></label>
  <input type="email" name="email"
    value="{{ old('email', $user->email ?? '') }}"
    class="form-control @error('email') is-invalid @enderror"
    style="font-size:13.5px;" placeholder="email@toko.com">
  @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
 
<div class="mb-3">
  <label class="form-label" style="font-size:13px; font-weight:500;">
    Kata Sandi {{ $user ? '(kosongkan jika tidak diubah)' : '' }} <span class="text-red-500">{{ $user ? '' : '*' }}</span>
  </label>
  <input type="password" name="password"
    class="form-control @error('password') is-invalid @enderror"
    style="font-size:13.5px;" placeholder="{{ $user ? 'Kosongkan jika tidak diubah' : 'Min. 8 karakter' }}"
    autocomplete="new-password">
  @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
 
<div class="mb-3">
  <label class="form-label" style="font-size:13px; font-weight:500;">
    Konfirmasi Kata Sandi {{ $user ? '(kosongkan jika tidak diubah)' : '' }}
  </label>
  <input type="password" name="password_confirmation"
    class="form-control" style="font-size:13.5px;"
    placeholder="{{ $user ? 'Kosongkan jika tidak diubah' : 'Ulangi kata sandi' }}"
    autocomplete="new-password">
</div>
 
<div class="mb-3">
  <label class="form-label" style="font-size:13px; font-weight:500;">Role <span class="text-red-500">*</span></label>
  <select name="role" class="form-select @error('role') is-invalid @enderror" style="font-size:13.5px;">
    <option value="">-- Pilih Role --</option>
    @foreach ($roles as $role)
      <option value="{{ $role->name }}"
        {{ old('role', $user?->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
        {{ ucfirst($role->name) }}
        @if ($role->name === 'owner') — Akses penuh
        @elseif ($role->name === 'admin') — Operasional harian
        @elseif ($role->name === 'kasir') — Transaksi POS saja
        @endif
      </option>
    @endforeach
  </select>
  @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
 
<div class="mb-4">
  <div class="form-check form-switch">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
      {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="isActive" style="font-size:13px;">Akun Aktif</label>
  </div>
  <div class="form-text" style="font-size:11px;">User nonaktif tidak dapat login ke sistem.</div>
</div>
 
<div class="flex gap-2">
  <button type="submit" class="btn-amber flex-1" style="padding:10px;">
    <i class="bi bi-check-lg mr-1"></i>
    {{ $user ? 'Simpan Perubahan' : 'Tambah User' }}
  </button>
  <a href="{{ route('users.index') }}" class="btn-outline-sm" style="font-size:13.5px; padding:9px 16px;">Batal</a>
</div>