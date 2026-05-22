{{-- resources/views/categories/_form.blade.php --}}

<div class="mb-3">
  <label class="form-label" style="font-size:13px; font-weight:500;">
    Nama Kategori <span class="text-red-500">*</span>
  </label>
  <input type="text" name="name"
    value="{{ old('name', $category->name ?? '') }}"
    class="form-control @error('name') is-invalid @enderror"
    style="font-size:13.5px;" placeholder="Contoh: Minuman, Makanan Ringan">
  @error('name')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-3">
  <label class="form-label" style="font-size:13px; font-weight:500;">Deskripsi</label>
  <textarea name="description" rows="3"
    class="form-control @error('description') is-invalid @enderror"
    style="font-size:13.5px;"
    placeholder="Deskripsi singkat kategori ini (opsional)">{{ old('description', $category->description ?? '') }}</textarea>
  @error('description')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="mb-4">
  <div class="form-check form-switch">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
      {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="isActive" style="font-size:13px;">Kategori Aktif</label>
  </div>
</div>

<div class="flex gap-2">
  <button type="submit" class="btn-amber flex-1" style="padding:10px;">
    <i class="bi bi-check-lg mr-1"></i>
    {{ $category ? 'Simpan Perubahan' : 'Tambah Kategori' }}
  </button>
  <a href="{{ route('categories.index') }}" class="btn-outline-sm" style="font-size:13.5px; padding:9px 16px;">
    Batal
  </a>
</div>