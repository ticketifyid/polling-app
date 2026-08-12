@extends('layouts.videotron')

@section('title', 'Hasil — ' . $nama_acara)

@push('head')
<style>
    /* Layar ini dibuat sepadan dengan step "pilih kandidat" di halaman voter:
       kolom 1080px yang ditengahkan, panel kaca dengan padding sama, dan kartu
       yang lebarnya lahir dari grid 5 kolom Bootstrap. Ukuran sengaja TIDAK
       memakai satuan vh — panel setinggi layar menutupi logo sponsor di atas
       dan tulisan "powered by" di bawah foto latar. */
    .result-screen {
        min-height: calc(100vh - (var(--space) * 2));
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Lebih lebar daripada kolom voter (1080px): di sini kartu tidak diklik,
       jadi ruang layar lebih baik dipakai untuk melonggarkan jarak antar kartu
       dan memperbesar angka suaranya. */
    .result-col {
        width: 100%;
        max-width: 1280px;
    }

    /* Judul berdiri langsung di atas foto, jadi butuh bobot dan halo putih tipis
       supaya tetap terbaca di bagian latar yang ramai — sama seperti
       .voter-title di halaman voter. */
    .result-head { margin-bottom: 24px; text-align: center; }
    .result-title {
        font-size: 36px;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.15;
        margin: 0;
        text-shadow: 0 1px 12px rgba(255, 255, 255, 0.95);
    }

    /* .container Bootstrap berhenti di 1140px pada layar 1200–1399px, yang akan
       memotong kolom 1280px di atas. Batasnya dilonggarkan supaya lebar kolom
       yang menentukan, bukan breakpoint. */
    .result-screen-wrap { max-width: 1360px; }

    .result-board { padding: 32px; }

    /* Kartu mengikuti .candidate-card: satu blok foto utuh, nama dan angka
       ditumpuk di kaki foto. Tanpa hover/selected — layar ini cuma dibaca. */
    .result-card {
        display: block;
        background: var(--paper);
        border: 1px solid var(--line);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    .result-photo {
        position: relative;
        aspect-ratio: 3 / 4;
        overflow: hidden;
        background: #F2F4F7;
        border-radius: 13px;   /* radius kartu (14px) dikurangi tebal border (1px) */
    }
    .result-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    /* Gelap bertingkat di kaki foto — alas baca untuk nama dan angka yang
       ditumpuk di atasnya. Lebih tinggi daripada di halaman voter karena yang
       ditampung juga lebih banyak. */
    .result-photo::after {
        content: '';
        position: absolute;
        inset: auto 0 0 0;
        height: 72%;
        background: linear-gradient(180deg,
            rgba(16, 24, 40, 0) 0%,
            rgba(16, 24, 40, 0.60) 45%,
            rgba(16, 24, 40, 0.92) 100%);
        pointer-events: none;
    }

    .result-label {
        position: absolute;
        inset: auto 0 0 0;
        z-index: 1;
        padding: 12px 10px 14px;
        text-align: center;
    }
    .result-name {
        display: block;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.3;
        color: #fff;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
    }
    .result-company {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.75);
    }
    /* Angka perolehan suara adalah isi utama layar ini: paling besar, paling
       terang, dan dipisahkan garis tipis dari nama di atasnya. */
    .result-count {
        display: block;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.25);
        font-size: 30px;
        font-weight: 700;
        line-height: 1.1;
        color: #fff;
        font-variant-numeric: tabular-nums;
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
    }
    .result-unit {
        display: block;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.75);
    }
</style>
@endpush

@section('content')
<main class="container page-main result-screen-wrap">
    <div class="result-screen">
        <div class="result-col">
            <header class="result-head">
                <h1 class="result-title">{{ $nama_acara }}</h1>
            </header>

            <div class="result-board floating-panel">
                <div class="row row-cols-5 g-4">
                    @foreach($candidates as $candidate)
                        <div class="col">
                            <div class="result-card h-100">
                                <div class="result-photo">
                                    <img src="{{ asset('storage/' . $candidate->foto) }}" alt="{{ $candidate->nama }}">
                                    <div class="result-label">
                                        <span class="result-name">{{ $candidate->nama }}</span>
                                        @if($candidate->company)
                                            <span class="result-company">{{ $candidate->company }}</span>
                                        @endif
                                        <span class="result-count">{{ $candidate->votes_count }}</span>
                                        <span class="result-unit">suara</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
