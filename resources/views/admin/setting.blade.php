@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('heading', 'Pengaturan')

@section('page-actions')
    @if($polling_dibuka)
        <span class="adm-badge adm-badge-on">Voting sedang dibuka</span>
    @else
        <span class="adm-badge adm-badge-off">Voting ditutup</span>
    @endif
@endsection

@section('content')
<form method="POST" action="{{ route('admin.setting.update') }}">
    @csrf
    @method('PUT')

    <div class="adm-card">
        <div class="adm-card-header">
            <h2 class="adm-card-title">Status polling</h2>
            <p class="adm-card-sub">Menentukan apakah voter bisa mengisi form di halaman depan.</p>
        </div>

        <div class="adm-card-body">
            <div class="adm-setting-row">
                <div class="adm-setting-main">
                    <p class="adm-setting-title">Polling dibuka</p>
                    <p class="adm-help">
                        Matikan untuk menutup voting. Halaman voter akan menampilkan pesan penutupan
                        dan tidak menerima isian baru.
                    </p>
                </div>
                <div class="adm-setting-control">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" role="switch" id="polling_dibuka"
                               name="polling_dibuka" value="1" {{ $polling_dibuka ? 'checked' : '' }}>
                        <label class="form-check-label" for="polling_dibuka">
                            {{ $polling_dibuka ? 'Dibuka' : 'Ditutup' }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="adm-card-header" style="border-top:1px solid var(--adm-line);">
            <h2 class="adm-card-title">Identitas acara</h2>
            <p class="adm-card-sub">Dipakai pada judul halaman, panel panitia, dan layar hasil.</p>
        </div>

        <div class="adm-card-body">
            <div class="adm-field">
                <label for="nama_acara" class="form-label">Nama acara</label>
                <input type="text" class="form-control @error('nama_acara') is-invalid @enderror"
                       id="nama_acara" name="nama_acara" value="{{ old('nama_acara', $nama_acara) }}" required>
                @error('nama_acara') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="adm-form-actions mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg"></i> Simpan pengaturan
        </button>
    </div>
</form>
@endsection
