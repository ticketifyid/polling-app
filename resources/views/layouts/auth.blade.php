@php
    $namaAcara = \App\Models\Setting::get('nama_acara', 'Polling Duta');
    $cssVersion = @filemtime(public_path('css/admin.css')) ?: 1;
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login Panitia') — {{ $namaAcara }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ $cssVersion }}">
    <style>
        /* Latar acara dipakai juga di layar login supaya pintu masuk panitia
           tidak terasa lepas dari halaman voter. Override-nya ditaruh di sini,
           bukan di css/admin.css, karena hanya layar ini yang berubah — panel
           panitia di baliknya tetap memakai palet --adm-*. */
        body { background-color: #DCE9F2; }
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

        /* Gradien biru bawaan .adm-auth dilepas — kalau tidak, ia menutupi foto. */
        .adm-auth { background: none; }

        /* Kartu login ikut jadi permukaan mengambang seperti panel di halaman
           voter: tembus pandang, ditopang blur, bukan warna pekat. */
        .adm-auth-card {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 24px 64px -12px rgba(16, 24, 40, 0.24);
        }
        @supports (backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px)) {
            .adm-auth-card {
                -webkit-backdrop-filter: blur(10px) saturate(1.25);
                backdrop-filter: blur(10px) saturate(1.25);
            }
        }
        /* Tanpa blur sebagai penopang, kartunya dikembalikan pekat supaya teks
           tetap terbaca di atas bagian latar yang ramai. */
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .adm-auth-card { background: rgba(255, 255, 255, 0.82); }
        }

        /* Latar sekarang terang, jadi teks putih di bawah kartu tidak lagi
           terbaca — dibalik jadi tinta gelap berhalo, sama seperti judul voter. */
        .adm-auth-foot {
            color: var(--adm-ink);
            font-weight: 600;
            text-shadow: 0 1px 12px rgba(255, 255, 255, 0.95);
        }
    </style>
    @stack('head')
</head>
<body>
    <main class="adm-auth">
        <div>
            <div class="adm-auth-card">
                @yield('content')
            </div>
            <p class="adm-auth-foot">{{ $namaAcara }}</p>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
