@php
    $namaAcara = \App\Models\Setting::get('nama_acara', 'Polling Duta');
    // Cache-buster berbasis waktu ubah file: setelah deploy, browser panitia
    // tidak menyajikan admin.css versi lama.
    $cssVersion = @filemtime(public_path('css/admin.css')) ?: 1;
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Panitia') — {{ $namaAcara }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ $cssVersion }}">
    @stack('head')
</head>
<body>
    <div class="adm-shell">
        @include('partials.admin-sidebar', ['namaAcara' => $namaAcara])

        <div class="adm-main">
            <header class="adm-topbar">
                <button class="adm-burger d-lg-none" type="button"
                        data-bs-toggle="offcanvas" data-bs-target="#adm-sidebar"
                        aria-controls="adm-sidebar" aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>

                <h1 class="adm-page-title">@yield('heading', 'Panel Panitia')</h1>

                @hasSection('page-actions')
                    <div class="adm-topbar-actions">@yield('page-actions')</div>
                @endif
            </header>

            @if(session('success') || session('error'))
                <div class="adm-alerts">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <i class="bi bi-check-circle-fill"></i>
                            <div>{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                        </div>
                    @endif
                </div>
            @endif

            <main class="adm-content">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
