@extends('layouts.admin')

@section('title', 'Edit Kandidat')
@section('heading', 'Edit kandidat')

@section('page-actions')
    <a href="{{ route('admin.kandidat.index') }}" class="btn btn-ghost">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.kandidat.update', $candidate) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="adm-card">
        <div class="adm-card-header">
            <h2 class="adm-card-title">{{ $candidate->nama }}</h2>
            <p class="adm-card-sub">Perubahan langsung berlaku di halaman voter bila kandidat aktif.</p>
        </div>

        <div class="adm-card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                           id="nama" name="nama" value="{{ old('nama', $candidate->nama) }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label for="company" class="form-label">Instansi / perusahaan <span class="adm-optional">opsional</span></label>
                    <input type="text" class="form-control @error('company') is-invalid @enderror"
                           id="company" name="company" value="{{ old('company', $candidate->company) }}">
                    @error('company')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <p class="adm-help">Ditampilkan di bawah nama kandidat. Kosongkan bila tidak perlu.</p>
                    @enderror
                </div>

                <div class="col-md-4 adm-field-sm">
                    <label for="urutan" class="form-label">Urutan</label>
                    <input type="number" class="form-control @error('urutan') is-invalid @enderror"
                           id="urutan" name="urutan" value="{{ old('urutan', $candidate->urutan) }}" min="0" required>
                    @error('urutan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @else
                        <p class="adm-help">Angka kecil tampil lebih dulu.</p>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label d-block">Foto</label>
                    <div class="adm-photo-row">
                        <img src="{{ asset('storage/' . $candidate->foto) }}" alt="Foto {{ $candidate->nama }}"
                             class="adm-photo-preview">
                        <div class="adm-photo-fields">
                            <label for="foto" class="form-label">Ganti foto</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror"
                                   id="foto" name="foto" accept="image/jpeg">
                            <p class="adm-help">Kosongkan bila foto tidak diubah. JPEG, maksimal 2MB, potret 3:4.</p>
                            @error('foto') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="adm-card-footer">
            <div class="adm-setting-row">
                <div class="adm-setting-main">
                    <p class="adm-setting-title">Tampilkan kandidat ini</p>
                    <p class="adm-help">Nonaktifkan untuk menyembunyikan kandidat dari halaman voter.</p>
                </div>
                <div class="adm-setting-control">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" role="switch" id="is_active"
                               name="is_active" value="1"
                               {{ old('is_active', $candidate->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="adm-form-actions mt-4">
        <a href="{{ route('admin.kandidat.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Perbarui kandidat
        </button>
    </div>
</form>
@endsection
