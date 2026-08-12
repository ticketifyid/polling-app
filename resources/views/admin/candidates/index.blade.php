@extends('layouts.admin')

@section('title', 'Kelola Kandidat')
@section('heading', 'Kandidat')

@section('page-actions')
    <a href="{{ route('admin.kandidat.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah kandidat
    </a>
@endsection

@section('content')
<div class="adm-card">
    <div class="adm-card-header">
        <h2 class="adm-card-title">Daftar kandidat</h2>
        <p class="adm-card-sub">
            {{ $candidates->count() }} kandidat terdaftar,
            {{ $candidates->where('is_active', true)->count() }} aktif dan tampil di halaman voter.
        </p>
    </div>

    @if($candidates->isEmpty())
        <div class="adm-empty">
            <div class="adm-empty-icon"><i class="bi bi-people"></i></div>
            <p class="adm-empty-title">Belum ada kandidat</p>
            <p class="adm-empty-text">Tambahkan kandidat agar voter punya pilihan saat polling dibuka.</p>
            <a href="{{ route('admin.kandidat.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah kandidat
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table adm-table align-middle">
                <thead>
                    <tr>
                        <th style="width:78px;">Foto</th>
                        <th>Nama</th>
                        <th style="width:100px;">Urutan</th>
                        <th style="width:130px;">Status</th>
                        <th style="width:280px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($candidates as $candidate)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/' . $candidate->foto) }}" alt="Foto {{ $candidate->nama }}"
                                     class="adm-thumb">
                            </td>
                            <td>
                                <span class="adm-cell-strong">{{ $candidate->nama }}</span>
                                @if($candidate->company)
                                    <span class="adm-cell-sub">{{ $candidate->company }}</span>
                                @endif
                            </td>
                            <td><span class="adm-order">{{ $candidate->urutan }}</span></td>
                            <td>
                                @if($candidate->is_active)
                                    <span class="adm-badge adm-badge-on">Aktif</span>
                                @else
                                    <span class="adm-badge adm-badge-off">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="adm-row-actions">
                                    <a href="{{ route('admin.kandidat.edit', $candidate) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.kandidat.toggle', $candidate) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            @if($candidate->is_active)
                                                <i class="bi bi-eye-slash"></i> Nonaktifkan
                                            @else
                                                <i class="bi bi-eye"></i> Aktifkan
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
