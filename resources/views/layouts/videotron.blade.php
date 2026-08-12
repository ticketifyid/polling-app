<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hasil')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* Layar hasil kini memakai palet dan latar yang sama dengan halaman
           voter (layouts/app.blade.php), bukan palet panel panitia — dua layar
           ini yang dilihat penonton, jadi keduanya harus terbaca sebagai satu
           tampilan. Token di bawah sengaja disalin, bukan di-import, supaya
           css/admin.css tidak perlu ikut dimuat di layar videotron. */
        :root {
            --ink:    #101828;
            --paper:  #FFFFFF;
            --muted:  #667085;
            --line:   #E4E7EC;
            --accent: #1B3A6B;
            --accent-2: #2E6BD4;
            --accent-soft: rgba(46, 107, 212, 0.10);

            --glass:      rgba(255, 255, 255, 0.12);
            --glass-line: rgba(255, 255, 255, 0.35);
            --glass-shadow: 0 24px 64px -12px rgba(16, 24, 40, 0.24);

            --shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.05);
            --shadow-lg: 0 12px 24px -6px rgba(16, 24, 40, 0.12), 0 4px 8px -4px rgba(16, 24, 40, 0.06);

            --space:  32px;
            --pad:    28px;

            --bs-body-color: var(--ink);
            --bs-border-radius-lg: 14px;
            --bs-font-sans-serif: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        .page-main { padding-block: var(--space); }

        body {
            font-family: var(--bs-font-sans-serif);
            color: var(--ink);
            -webkit-font-smoothing: antialiased;
            background-color: #DCE9F2;
            min-height: 100vh;
        }

        /* Lapisan latar tetap seukuran viewport — sama persis dengan halaman
           voter, jadi gambar utuh baik windowed maupun F11. */
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

        .floating-panel {
            background: var(--glass);
            border: 1px solid var(--glass-line);
            border-radius: 20px;
            box-shadow: var(--glass-shadow);
        }
        @supports (backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px)) {
            .floating-panel {
                -webkit-backdrop-filter: blur(10px) saturate(1.25);
                backdrop-filter: blur(10px) saturate(1.25);
            }
        }
        @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
            .floating-panel { background: rgba(255, 255, 255, 0.82); }
        }

        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; }
        }
    </style>
    @stack('head')
</head>
<body>
    @yield('content')
</body>
</html>
