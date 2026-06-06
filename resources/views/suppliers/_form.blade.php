<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

  {{-- Kolom kiri --}}
  <div class="lg:col-span-2">

    <div class="card p-4">
      <h6 class="font-semibold mb-3" style="font-size:14px;">Informasi Supplier</h6>

      <div class="mb-3">
        <label class="form-label" style="font-size:13px; font-weight:500;">Nama Supplier <span class="text-red-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}"
          class="form-control @error('name') is-invalid @enderror"
          style="font-size:13.5px;" placeholder="Masukkan nama supplier" autofocus>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">No. Telepon / WhatsApp</label>
          <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}"
            class="form-control @error('phone') is-invalid @enderror"
            style="font-size:13.5px;" placeholder="Opsional">
          @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="form-label" style="font-size:13px; font-weight:500;">Email</label>
          <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}"
            class="form-control @error('email') is-invalid @enderror"
            style="font-size:13.5px;" placeholder="Opsional">
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-3">
        <label class="form-label" style="font-size:13px; font-weight:500;">Alamat Lengkap</label>
        <textarea name="address" rows="3"
          class="form-control @error('address') is-invalid @enderror"
          style="font-size:13.5px;" placeholder="Alamat supplier (opsional)">{{ old('address', $supplier->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
      </div>
    </div>

  </div>

  {{-- Kolom kanan --}}
  <div>

    <div class="card p-4">
      <h6 class="font-semibold mb-3" style="font-size:14px;">Status</h6>
      <div class="form-check form-switch">
        <input type="hidden" name="is_active" value="0">
        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
          {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="isActive" style="font-size:13px;">Supplier Aktif</label>
      </div>
      <div class="form-text" style="font-size:11px;">Jika nonaktif, tidak bisa dipilih untuk pembelian baru.</div>
    </div>

    <div class="mt-3 flex flex-col gap-2">
      <button type="submit" class="btn-amber w-full" style="padding:11px;">
        <i class="bi bi-check-lg mr-1"></i>
        {{ $supplier ? 'Simpan Perubahan' : 'Tambah Supplier' }}
      </button>
      <a href="{{ route('suppliers.index') }}" class="btn-outline-sm w-full text-center" style="font-size:13.5px; padding:10px;">
        Batal
      </a>
    </div>

  </div>

</div>
