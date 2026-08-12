@extends('layouts.app')

@section('title', 'Registrasi & Voting')

@section('content')
<div class="voter-screen">
    <div class="voter-col">
        <header class="voter-head">
            <h1 class="voter-title">{{ \App\Models\Setting::get('nama_acara', 'Pemilihan Duta') }}</h1>
        </header>

        @if($errors->any())
            <div class="alert alert-danger block">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="vote-form" method="POST" action="{{ route('vote.store') }}">
            @csrf

            {{-- STEP 1 --}}
            <div id="step-1" class="voter-panel floating-panel">
                {{-- Penanda langkah ikut tersembunyi bersama stepnya, jadi tidak perlu JS. --}}
                <div class="steps" aria-hidden="true">
                    <span class="steps-item is-on"><span class="steps-dot">1</span>Data diri</span>
                    <span class="steps-line"></span>
                    <span class="steps-item"><span class="steps-dot">2</span>Pilih kandidat</span>
                </div>

                <div class="field field-last">
                    <label for="nama" class="form-label">Nama lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="nama" name="nama"
                               value="{{ old('nama') }}" placeholder="Nama sesuai KTP" required>
                    </div>
                </div>

                <button type="button" id="btn-next" class="btn btn-primary btn-action">
                    Lanjut ke pilihan
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>

            {{-- STEP 2 --}}
            <div id="step-2" class="d-none">
                <div class="voter-panel floating-panel">
                    <div class="steps" aria-hidden="true">
                        <span class="steps-item is-done"><span class="steps-dot"><i class="bi bi-check-lg"></i></span>Data diri</span>
                        <span class="steps-line is-on"></span>
                        <span class="steps-item is-on"><span class="steps-dot">2</span>Pilih kandidat</span>
                    </div>

                    <div class="row row-cols-5 g-3">
                        @foreach($candidates as $candidate)
                            <div class="col">
                                <input type="radio" class="btn-check" name="candidate_id"
                                       id="cand-{{ $candidate->id }}" value="{{ $candidate->id }}"
                                       autocomplete="off" required>
                                <label class="candidate-card h-100" for="cand-{{ $candidate->id }}">
                                    <div class="candidate-photo">
                                        <img src="{{ asset('storage/' . $candidate->foto) }}"
                                             alt="{{ $candidate->nama }}">
                                        <span class="candidate-check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                                        {{-- Nama ditumpuk di atas foto: kartu jadi satu blok utuh
                                             dan tinggi barisnya lebih hemat. --}}
                                        <div class="candidate-label">
                                            <span class="candidate-name">{{ $candidate->nama }}</span>
                                            @if($candidate->company)
                                                <span class="candidate-company">{{ $candidate->company }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{-- Urutan DOM mengikuti urutan visual (kembali di kiri) supaya
                         urutan Tab tidak melompat. --}}
                    <div class="step2-actions">
                        <button type="button" id="btn-back" class="btn btn-outline-secondary btn-action">
                            <i class="bi bi-arrow-left"></i>
                            Kembali
                        </button>
                        <button type="submit" class="btn btn-primary btn-action">
                            <i class="bi bi-send"></i>
                            Kirim pilihan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('head')
<style>
    /* Judul berdiri langsung di atas foto, jadi butuh bobot dan halo putih tipis
       supaya tetap terbaca di bagian latar yang ramai. */
    .voter-head { margin-bottom: 24px; text-align: center; }
    .voter-title {
        font-size: 36px;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1.15;
        margin: 0;
        text-shadow: 0 1px 12px rgba(255, 255, 255, 0.95);
    }

    /* Penanda langkah — lingkaran bernomor, garis penghubung, label ringkas. */
    .steps {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 28px;
    }
    .steps-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
        color: var(--muted);
        white-space: nowrap;
    }
    .steps-dot {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: var(--line);
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
    }
    .steps-item.is-on { color: var(--ink); }
    .steps-item.is-on .steps-dot {
        background: linear-gradient(180deg, var(--accent-2) 0%, var(--accent) 100%);
        color: var(--paper);
        box-shadow: var(--ring);
    }
    .steps-item.is-done { color: var(--ink); }
    .steps-item.is-done .steps-dot {
        background: var(--accent-soft);
        color: var(--accent-2);
    }
    .steps-line {
        flex: 1;
        height: 2px;
        border-radius: 1px;
        background: var(--line);
    }
    .steps-line.is-on { background: var(--accent-2); }

    /* Input setinggi tombol supaya kolomnya terbaca sebagai satu blok. */
    .voter-panel .form-control {
        height: 52px;
        min-height: 52px;
        padding-inline: 16px;
    }

    .field { margin-bottom: 24px; }
    .field-last { margin-bottom: var(--space); }

    /* Dua tombol bersebelahan: "Kembali" di kiri, "Kirim pilihan" di kanan.
       Tindakan utama dibuat dua kali lebih lebar supaya jelas mana yang dituju.
       Keduanya ditengahkan dan dibatasi 520px — tombol selebar 1080px susah dituju. */
    .step2-actions {
        margin-top: var(--space);
        display: flex;
        gap: 12px;
        max-width: 520px;
        margin-inline: auto;
    }
    .step2-actions .btn-action { width: auto; flex: 1; }
    .step2-actions [type="submit"] { flex: 2; }

    /* Kartu kandidat. Ukuran kotak tidak berubah saat terpilih — status dibawa
       oleh cincin (box-shadow), bukan border, supaya kartu tidak "melompat". */
    .candidate-card {
        display: block;
        width: 100%;
        cursor: pointer;
        background: var(--paper);
        border: 1px solid var(--line);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    }
    .candidate-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
        border-color: #D0D5DD;
    }
    .btn-check:checked + .candidate-card {
        border-color: var(--accent-2);
        box-shadow: 0 0 0 3px var(--accent-soft), var(--shadow-lg);
        transform: translateY(-3px);
    }
    .btn-check:focus-visible + .candidate-card { box-shadow: var(--ring), var(--shadow-lg); }

    .candidate-photo {
        position: relative;
        aspect-ratio: 3 / 4;
        overflow: hidden;
        background: #F2F4F7;
        border-radius: 13px;   /* radius kartu (14px) dikurangi tebal border (1px) */
    }
    .candidate-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .3s ease;
    }
    .candidate-card:hover .candidate-photo img { transform: scale(1.04); }

    /* Gelap bertingkat di kaki foto — alas baca untuk nama yang ditumpuk
       di atasnya. Cukup pekat supaya nama tetap terbaca di foto latar terang. */
    .candidate-photo::after {
        content: '';
        position: absolute;
        inset: auto 0 0 0;
        height: 62%;
        background: linear-gradient(180deg,
            rgba(16, 24, 40, 0) 0%,
            rgba(16, 24, 40, 0.55) 55%,
            rgba(16, 24, 40, 0.88) 100%);
        pointer-events: none;
    }

    .candidate-check {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 1;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(180deg, var(--accent-2) 0%, var(--accent) 100%);
        color: var(--paper);
        font-size: 15px;
        line-height: 1;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(16, 24, 40, 0.35);
    }
    .btn-check:checked + .candidate-card .candidate-check { display: flex; }

    .candidate-label {
        position: absolute;
        inset: auto 0 0 0;
        z-index: 1;
        padding: 12px 10px 14px;
        text-align: center;
    }
    .candidate-name {
        display: block;
        line-height: 1.3;
        color: #fff;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
    }
    .candidate-company {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.75);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/vote.js') }}"></script>
@endpush
