<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Polling Duta') — {{ \App\Models\Setting::get('nama_acara', 'Polling Duta') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink:    #101828;
            --paper:  #FFFFFF;
            --muted:  #667085;
            --line:   #E4E7EC;
            --accent: #1B3A6B;
            --accent-2: #2E6BD4;   /* aksen terang untuk gradien dan sorotan */
            --accent-soft: rgba(46, 107, 212, 0.10);
            --danger: #B42318;

            /* Permukaan mengambang di atas foto latar. Sengaja tembus pandang
               supaya latar acara masih terbaca di balik panel; keterbacaan teks
               ditopang blur pada .floating-panel, bukan oleh pekatnya warna. */
            --glass:      rgba(255, 255, 255, 0.12);
            --glass-line: rgba(255, 255, 255, 0.35);
            --glass-shadow: 0 24px 64px -12px rgba(16, 24, 40, 0.24);

            /* Bayangan berlapis: satu tipis untuk mempertegas tepi,
               satu lebar dan lembut untuk kesan mengambang. */
            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.05);
            --shadow-md: 0 4px 8px -2px rgba(16, 24, 40, 0.08), 0 2px 4px -2px rgba(16, 24, 40, 0.05);
            --shadow-lg: 0 12px 24px -6px rgba(16, 24, 40, 0.12), 0 4px 8px -4px rgba(16, 24, 40, 0.06);
            --ring: 0 0 0 4px var(--accent-soft);

            --space:  32px;
            --pad:    28px;

            --bs-primary: var(--accent);
            --bs-body-color: var(--ink);
            --bs-body-bg: var(--paper);
            --bs-border-color: var(--line);
            --bs-border-radius: 10px;
            --bs-border-radius-sm: 8px;
            --bs-border-radius-lg: 14px;
            --bs-border-radius-xl: 20px;
            --bs-font-sans-serif: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        body {
            font-family: var(--bs-font-sans-serif);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;

            /* Warna cadangan kalau gambar belum termuat, diambil dari langit
               di bagian atas foto supaya tidak berkedip putih. */
            background-color: #DCE9F2;
            min-height: 100vh;
        }

        /* Latar acara dipasang sebagai lapisan tetap seukuran viewport, bukan
           background <body>. Dengan begitu ukurannya selalu mengikuti jendela —
           windowed maupun F11 — dan tidak ikut memanjang saat halaman di-scroll.
           `100% 100%` dipilih supaya seluruh gambar utuh: logo sponsor di atas
           dan "powered by" di bawah tidak pernah terpotong. */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: -1;
            background-image: url('{{ asset('img/BACKGROUND.png') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* --- Permukaan mengambang --- */
        .floating-panel {
            background: var(--glass);
            border: 1px solid var(--glass-line);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
            padding: var(--pad);
        }
        /* Blur hanya kalau browser mendukung; tanpa itu panel tetap terbaca
           karena --glass sudah cukup pekat. */
        @supports (backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px)) {
            .floating-panel {
                -webkit-backdrop-filter: blur(10px) saturate(1.25);
                backdrop-filter: blur(10px) saturate(1.25);
            }
        }
        /* Browser tanpa backdrop-filter tidak punya blur sebagai penopang, jadi
           panelnya dikembalikan ke tingkat pekat yang lama supaya teks aman. */
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .floating-panel, .alert { background: rgba(255, 255, 255, 0.82); }
        }

        /* --- Tipografi: peran dibedakan lewat ukuran dan bobot --- */
        .page-title {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.02em;
            line-height: 1.25;
            margin: 0;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.01em;
            margin: 0 0 20px;
        }
        .candidate-name {
            font-size: 15px;
            font-weight: 600;
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 8px;
        }
        .help-text {
            font-size: 14px;
            font-weight: 400;
            color: var(--muted);
            margin: 0;
        }

        .page-main { padding-block: var(--space); }

        /* Kolom voter: satu kolom sempit yang mengambang di tengah layar. */
        .voter-screen {
            min-height: calc(100vh - (var(--space) * 2));
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .voter-col {
            width: 100%;
            max-width: 440px;
        }
        /* Step pemilihan kandidat butuh kolom lebar (5 kartu mendatar).
           Kelas ini dipasang/dilepas oleh public/js/vote.js. */
        .voter-col.is-wide { max-width: 1080px; }

        /* --- Ritme vertikal: jarak antar blok minimal 32px --- */
        .stack > * + * { margin-top: var(--space); }
        .block { margin-bottom: var(--space); }

        /* --- Permukaan --- */
        .card {
            border: 1px solid var(--line);
            border-radius: var(--bs-border-radius-lg);
            box-shadow: var(--shadow-md);
            background: var(--paper);
        }
        .card-body { padding: var(--pad); }

        /* --- Kontrol form --- */
        .form-control, .form-select {
            min-height: 44px;
            border-color: var(--line);
            font-size: 16px;
            background-color: var(--paper);
            box-shadow: var(--shadow-sm);
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .form-control::placeholder { color: #98A2B3; }
        /* Cincin fokus lembut, bukan garis tebal — penanda fokus tetap jelas. */
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-2);
            box-shadow: var(--ring);
        }
        .input-group-text {
            background: var(--paper);
            border-color: var(--line);
            color: var(--muted);
            box-shadow: var(--shadow-sm);
        }
        /* Ikon dan input dibaca sebagai satu kontrol: sekat di antaranya dihapus
           dan cincin fokus dipasang ke seluruh grup. */
        .input-group > .form-control { border-left: 0; padding-left: 4px; }
        .input-group:focus-within .input-group-text { border-color: var(--accent-2); }
        .input-group:focus-within > .form-control {
            border-color: var(--accent-2);
            box-shadow: none;
        }
        .input-group:focus-within { border-radius: var(--bs-border-radius); box-shadow: var(--ring); }
        .input-group:focus-within .input-group-text i { color: var(--accent-2); }
        .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }
        .form-check-input:focus {
            border-color: var(--accent);
            box-shadow: none;
        }
        /* Switch dijaga 44px tanpa mengubah struktur form-check Bootstrap. */
        .form-switch {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            padding-left: 0;
        }
        .form-switch .form-check-input {
            width: 44px;
            height: 24px;
            margin: 0;
            float: none;
        }
        .form-check-label { margin: 0; }

        /* --- Tombol: semua area tekan minimal 44px --- */
        .btn {
            min-height: 44px;
            font-size: 15px;
            font-weight: 600;
            border-radius: var(--bs-border-radius);
            background-image: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform .12s ease, box-shadow .15s ease, background-color .15s ease;
        }
        .btn:active { transform: translateY(1px); }
        .btn:focus-visible { box-shadow: var(--ring); }
        .btn-sm { min-height: 44px; font-size: 14px; padding-inline: 14px; }
        .btn-primary {
            --bs-btn-bg: var(--accent);
            --bs-btn-border-color: var(--accent);
            --bs-btn-hover-bg: #16305A;
            --bs-btn-hover-border-color: #16305A;
            --bs-btn-active-bg: #16305A;
            --bs-btn-active-border-color: #16305A;
            --bs-btn-disabled-bg: var(--accent);
            --bs-btn-disabled-border-color: var(--accent);
            background-image: linear-gradient(180deg, var(--accent-2) 0%, var(--accent) 100%);
            border-color: var(--accent);
            box-shadow: var(--shadow-md);
        }
        .btn-primary:hover {
            background-image: linear-gradient(180deg, #3B79E0 0%, #1F4278 100%);
            box-shadow: var(--shadow-lg);
        }
        .btn-outline-secondary {
            --bs-btn-color: var(--ink);
            --bs-btn-bg: var(--paper);
            --bs-btn-border-color: var(--line);
            --bs-btn-hover-color: var(--ink);
            --bs-btn-hover-bg: #F9FAFB;
            --bs-btn-hover-border-color: #D0D5DD;
            --bs-btn-active-color: var(--ink);
            --bs-btn-active-bg: #F9FAFB;
            --bs-btn-active-border-color: #D0D5DD;
            box-shadow: var(--shadow-sm);
        }
        /* Tombol tindakan utama: melebar penuh, tinggi 52px */
        .btn-action {
            width: 100%;
            min-height: 52px;
            font-size: 16px;
            font-weight: 600;
        }

        /* --- Pesan --- */
        /* Pesan ikut mengambang, kalau tidak ia terbaca menempel di foto. */
        .alert {
            border-radius: var(--bs-border-radius-lg);
            border: 1px solid var(--glass-line);
            font-size: 15px;
            padding: 16px var(--pad);
            background: var(--glass);
            box-shadow: var(--glass-shadow);
        }
        @supports (backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px)) {
            .alert {
                -webkit-backdrop-filter: blur(10px) saturate(1.25);
                backdrop-filter: blur(10px) saturate(1.25);
            }
        }
        .alert-success { color: var(--ink); }
        .alert-danger {
            border-color: var(--danger);
            color: var(--danger);
        }
        .invalid-feedback { font-size: 14px; color: var(--danger); }
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--danger);
            background-image: none;
            box-shadow: 0 0 0 4px rgba(180, 35, 24, 0.10);
        }
        /* Kolom salah di dalam input-group: ikonnya ikut memerah. */
        .input-group:has(.is-invalid) .input-group-text {
            border-color: var(--danger);
            color: var(--danger);
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; }
        }

    </style>
    @stack('head')
</head>
<body>
    {{-- Layout ini khusus halaman voter. Area admin memakai layouts/admin.blade.php
         (sidebar) dan layouts/auth.blade.php (login). --}}
    <main class="container page-main">
        @if(session('success'))
            <div class="alert alert-success block">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger block">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
