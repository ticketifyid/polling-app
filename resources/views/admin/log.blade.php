@extends('layouts.admin')

@section('title', 'Log Isian')
@section('heading', 'Log isian voter')

@section('page-actions')
    <span class="adm-badge-count">
        <i class="bi bi-check2-square"></i> {{ number_format($votes->total(), 0, ',', '.') }} isian
    </span>
@endsection

@section('content')
<div class="adm-card">
    <div class="adm-card-header">
        <form method="GET" action="{{ route('admin.log') }}">
            <div class="adm-setting-row">
                <div class="adm-setting-main">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="q" class="form-control"
                               placeholder="Cari nama…" value="{{ $q }}"
                               aria-label="Cari nama">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </div>
                </div>
                @if($q !== '')
                    <div class="adm-setting-control">
                        <a href="{{ route('admin.log') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Reset
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    @if($votes->isEmpty())
        <div class="adm-empty">
            <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
            @if($q !== '')
                <p class="adm-empty-title">Tidak ada hasil</p>
                <p class="adm-empty-text">Tidak ditemukan isian yang cocok dengan “{{ $q }}”.</p>
                <a href="{{ route('admin.log') }}" class="btn btn-outline-secondary">Tampilkan semua</a>
            @else
                <p class="adm-empty-title">Belum ada isian</p>
                <p class="adm-empty-text">Isian voter akan muncul di sini begitu polling berjalan.</p>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table adm-table align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nama</th>
                        <th>Kandidat</th>
                        <th style="width:150px;">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($votes as $vote)
                        <tr>
                            <td class="adm-row-num">
                                {{ $loop->iteration + ($votes->firstItem() ? $votes->firstItem() - 1 : 0) }}
                            </td>
                            <td class="adm-cell-strong">{{ $vote->nama }}</td>
                            <td>{{ $vote->candidate?->nama ?? '—' }}</td>
                            <td>
                                {{ $vote->created_at->format('d/m/Y') }}
                                <span class="adm-cell-sub">{{ $vote->created_at->format('H:i:s') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($votes->hasPages())
            <div class="adm-card-footer">
                {{ $votes->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
