@extends('layouts.videotron')

@section('title', 'Hasil — ' . $nama_acara)

@push('head')
<style>
    /* Layar ini dibuat sepadan dengan step "pilih kandidat" di halaman voter:
       kolom yang ditengahkan, panel kaca dengan padding sama, dan kartu yang
       lebarnya lahir dari grid 5 kolom Bootstrap. Ukuran sengaja TIDAK memakai
       satuan vh — panel setinggi layar menutupi logo sponsor di atas dan
       tulisan "powered by" di bawah foto latar.

       Halaman punya tiga babak yang dijalankan public/js/result.js:
         1. terkunci  — angka disamarkan, gembok menutup papan
         2. mengocok  — angka berputar acak lalu mendarat di nilai asli
         3. terbuka   — sembilan kartu memudar, satu terfavorit membesar ke
                        tengah, sisanya turun jadi satu baris kecil
       Tanpa JavaScript, ketiganya tidak pernah aktif dan papan tampil apa
       adanya — itu sebabnya keadaan awal di markup adalah keadaan akhir yang
       bisa dibaca, bukan keadaan tersembunyi. */
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
    /* .container Bootstrap berhenti di 1140px pada layar 1200–1399px, yang akan
       memotong kolom 1280px di atas. Batasnya dilonggarkan supaya lebar kolom
       yang menentukan, bukan breakpoint. */
    .result-screen-wrap { max-width: 1360px; }

    /* Judul berdiri langsung di atas foto, jadi butuh bobot dan halo putih tipis
       supaya tetap terbaca di bagian latar yang ramai — sama seperti
       .voter-title di halaman voter. */
    .result-head { margin-bottom: 18px; text-align: center; }
    .result-title {
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.15;
        margin: 0;
        text-shadow: 0 1px 12px rgba(255, 255, 255, 0.95);
    }

    .result-board {
        position: relative;
        padding: 24px;
    }

    /* Panel hasil sengaja TIDAK memakai backdrop-filter, beda dari panel di
       halaman voter. Blur latar itu dihitung ulang oleh GPU setiap kali ada
       yang berubah di dalamnya, dan biayanya naik mengikuti luas layar — di
       LED besar inilah sumber patah-patahnya. Panelnya tetap tembus pandang,
       hanya saja lewat warna, bukan blur: nyaris gratis untuk digambar. */
    .result-board.floating-panel {
        background: rgba(255, 255, 255, 0.24);
        -webkit-backdrop-filter: none;
        backdrop-filter: none;
    }

    /* Tinggi kartu lahir dari lebarnya (rasio 3:4), jadi satu-satunya cara
       memendekkan papan tanpa menyempitkan panel adalah membatasi lebar kartu
       lalu menengahkannya di dalam kolom. Sisa ruang kolom justru jadi jarak
       antar kartu — papan lebih pendek, logo sponsor di atas foto latar tidak
       ketutupan, dan kartunya malah terasa lebih lega. */
    .result-grid .result-card {
        max-width: 196px;
        margin-inline: auto;
    }

    /* ==== Kartu dasar ================================================== */
    /* Mengikuti .candidate-card: satu blok foto utuh, nama dan angka ditumpuk
       di kaki foto. Tanpa hover/selected — layar ini cuma dibaca. */
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

    /* ==== Babak 1: terkunci ============================================ */
    /* Papan diburamkan, bukan disembunyikan: penonton tahu ada sepuluh kandidat
       di baliknya, tapi belum bisa membaca satu angka pun. */
    /* Angka sudah disamarkan jadi "??" oleh result.js, jadi papan tidak perlu
       diburamkan sama sekali — cukup diredupkan. `filter: blur()` di atas
       sepuluh foto adalah efek termahal di halaman ini; opacity dikerjakan
       compositor tanpa menggambar ulang apa pun. */
    .is-locked .result-grid {
        opacity: 0.3;
        pointer-events: none;
        transition: opacity .5s ease;
    }
    .result-lock {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 18px;
        border: 0;
        border-radius: 20px;
        background: transparent;
        color: var(--ink);
        cursor: pointer;
        transition: opacity .5s ease;
    }
    .result-lock[hidden] { display: none; }
    .result-lock-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 112px;
        height: 112px;
        border-radius: 50%;
        font-size: 46px;
        color: #fff;
        background: linear-gradient(180deg, var(--accent-2) 0%, var(--accent) 100%);
        box-shadow: 0 18px 40px -12px rgba(16, 24, 40, 0.55);
        animation: lock-breathe 2.4s ease-in-out infinite;
    }
    .result-lock-text {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.01em;
        text-shadow: 0 1px 12px rgba(255, 255, 255, 0.95);
    }
    .result-lock:hover .result-lock-icon { transform: scale(1.06); }
    .result-lock:focus-visible { box-shadow: var(--ring); outline: none; }
    /* Denyut pelan supaya penonton paham gemboknya bisa diklik. Hanya transform
       yang bergerak — versi sebelumnya ikut menganimasikan box-shadow, dan
       bayangan harus digambar ulang tiap frame. */
    @keyframes lock-breathe {
        0%, 100% { transform: scale(1); }
        50%      { transform: scale(1.07); }
    }
    /* Gembok terbuka sesaat sebelum angka mulai berputar. */
    .result-lock.is-opening {
        opacity: 0;
        pointer-events: none;
    }
    .result-lock.is-opening .result-lock-icon { animation: none; transform: scale(1.5) rotate(-12deg); }

    /* ==== Babak 2: mengocok ============================================ */
    /* Getar per kartu dihapus: sepuluh elemen yang bergerak terus-menerus
       selama tiga detik adalah beban tetap yang paling terasa di mesin lemah.
       Ketegangan babak ini toh sudah dibawa oleh angka yang berputar. Sebagai
       gantinya papan diberi satu denyut sorot — satu elemen, bukan sepuluh. */
    .is-rolling .result-count { color: #fff; }
    .is-rolling .result-board,
    .result-board.is-rolling {
        animation: board-pulse 1.1s ease-in-out infinite;
    }
    @keyframes board-pulse {
        0%, 100% { background-color: rgba(255, 255, 255, 0.24); }
        50%      { background-color: rgba(255, 255, 255, 0.34); }
    }
    /* Angka mendarat di nilai aslinya — sekali sentak, lalu diam. */
    .result-count.is-landed { animation: count-pop .45s cubic-bezier(.2, 1.4, .4, 1); }
    @keyframes count-pop {
        0%   { transform: scale(1); }
        45%  { transform: scale(1.28); }
        100% { transform: scale(1); }
    }

    /* ==== Babak 3: terbuka ============================================= */
    /* Sembilan yang kalah memudar dan mengecil; stagger diatur dari JS lewat
       custom property --i supaya urutan padamnya terbaca sebagai gelombang. */
    /* Sembilan kartu tidak lagi dipadamkan satu per satu. Stagger itu berarti
       sembilan animasi berjalan bersamaan di sembilan layer terpisah; sekarang
       seluruh grid memudar sebagai SATU elemen. Efek yang dilihat penonton
       hampir sama, biayanya sepersembilan. */
    .result-grid.is-out {
        opacity: 0;
        transition: opacity .5s ease;
        pointer-events: none;
    }

    .result-final { display: none; }
    .result-final.is-shown { display: block; }

    /* Panggung pemenang: kartu yang sama, dibesarkan dan ditengahkan. */
    .result-spotlight {
        display: flex;
        justify-content: center;
        animation: spotlight-in .9s cubic-bezier(.16, 1, .3, 1) both;
    }
    .result-spotlight .result-card {
        width: 244px;
        border-color: var(--accent-2);
        box-shadow: 0 0 0 4px var(--accent-soft), 0 32px 64px -16px rgba(16, 24, 40, 0.45);
    }
    /* Pemenang dibaca dari jauh, jadi tulisannya naik kelas — bukan cuma
       kartunya yang membesar. */
    .result-spotlight .result-name  { font-size: 23px; }
    .result-spotlight .result-company { font-size: 13px; margin-top: 5px; }
    .result-spotlight .result-count { font-size: 46px; margin-top: 8px; padding-top: 8px; }
    .result-spotlight .result-unit  { font-size: 13px; }
    .result-spotlight .result-label { padding: 16px 14px 18px; }
    @keyframes spotlight-in {
        from { opacity: 0; transform: scale(0.55); }
        to   { opacity: 1; transform: scale(1); }
    }

    /* Pita "terfavorit" di atas kartu pemenang. */
    .result-crown {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--accent);
        text-shadow: 0 1px 12px rgba(255, 255, 255, 0.95);
        animation: spotlight-in .9s cubic-bezier(.16, 1, .3, 1) both;
        animation-delay: .25s;
    }
    .result-crown i { font-size: 22px; color: #E9B949; }

    /* Sembilan sisanya: satu baris kecil, cuma pelengkap konteks. */
    .result-runners {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
        padding-top: 18px;
        border-top: 1px solid var(--glass-line);
        animation: runners-in .7s ease both;
        animation-delay: .7s;
    }
    .result-runners .result-card { width: 104px; }
    .result-runners .result-label { padding: 8px 6px 9px; }
    .result-runners .result-name  { font-size: 11px; }
    .result-runners .result-company { display: none; }
    .result-runners .result-count {
        font-size: 18px;
        margin-top: 5px;
        padding-top: 5px;
    }
    .result-runners .result-unit { display: none; }
    @keyframes runners-in {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Layar yang lebih pendek: kartu pemenang dikecilkan supaya baris sembilan
       sisanya tidak terdorong keluar layar. */
    @media (max-height: 800px) {
        .result-grid .result-card { max-width: 168px; }
        .result-spotlight .result-card { width: 208px; }
        .result-spotlight .result-count { font-size: 38px; }
        .result-runners .result-card { width: 84px; }
    }

    /* Semua gerak di atas hanya hiasan; kalau penonton minta gerak dikurangi,
       babaknya tetap jalan, cuma tanpa animasi. */
    @media (prefers-reduced-motion: reduce) {
        .result-lock-icon, .result-spotlight, .result-crown, .result-runners,
        .result-board.is-rolling, .result-count.is-landed {
            animation: none !important;
        }
        .result-grid.is-out { transition: none; }
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

            <div class="result-board floating-panel" id="result-board">
                {{-- Gembok disisipkan JS-lah yang menyalakannya (atribut hidden
                     dilepas di result.js). Tanpa JS, papan tampil apa adanya. --}}
                <button type="button" class="result-lock" id="result-lock" hidden>
                    <span class="result-lock-icon"><i class="bi bi-lock-fill"></i></span>
                    <span class="result-lock-text">Klik untuk membuka hasil</span>
                </button>

                <div class="row row-cols-5 g-4" id="result-grid">
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
                                        {{-- data-count dibaca result.js: nilai asli tetap ada di DOM
                                             sementara teksnya disamarkan selama babak terkunci. --}}
                                        <span class="result-count" data-count="{{ $candidate->votes_count }}">{{ $candidate->votes_count }}</span>
                                        <span class="result-unit">suara</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Panggung akhir. Isinya disalin dari grid di atas oleh result.js
                     setelah gembok dibuka, jadi tidak ada markup kandidat yang
                     ditulis dua kali. --}}
                <div class="result-final" id="result-final" aria-live="polite">
                    <div class="result-crown">
                        <i class="bi bi-trophy-fill"></i>
                        <span>Relawan terfavorit</span>
                    </div>
                    <div class="result-spotlight" id="result-spotlight"></div>
                    <div class="result-runners" id="result-runners"></div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/result.js') }}"></script>
@endpush
