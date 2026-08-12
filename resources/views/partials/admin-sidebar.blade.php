{{--
    Sidebar admin. Memakai offcanvas responsif Bootstrap 5.3: pada >=lg elemen ini
    tampil statis sebagai kolom kiri, di bawah lg berubah jadi drawer yang dibuka
    tombol hamburger di topbar. Tidak butuh JavaScript tambahan.
--}}
<aside class="offcanvas-lg offcanvas-start adm-sidebar" tabindex="-1" id="adm-sidebar"
       aria-labelledby="adm-sidebar-label">
    <div class="offcanvas-body">
        <div class="adm-brand">
            <span class="adm-brand-mark"><i class="bi bi-award"></i></span>
            <span class="adm-brand-text">
                <span class="adm-brand-name" id="adm-sidebar-label">{{ $namaAcara }}</span>
                <span class="adm-brand-sub">Panel Panitia</span>
            </span>
            <button type="button" class="adm-brand-close d-lg-none"
                    data-bs-dismiss="offcanvas" data-bs-target="#adm-sidebar" aria-label="Tutup menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <p class="adm-nav-label">Menu</p>

        <nav class="nav flex-column adm-nav">
            <a href="{{ route('admin.kandidat.index') }}"
               class="adm-nav-link {{ request()->routeIs('admin.kandidat.*') ? 'is-active' : '' }}"
               @if(request()->routeIs('admin.kandidat.*')) aria-current="page" @endif>
                <i class="bi bi-people"></i> Kandidat
            </a>

            <a href="{{ route('admin.log') }}"
               class="adm-nav-link {{ request()->routeIs('admin.log') ? 'is-active' : '' }}"
               @if(request()->routeIs('admin.log')) aria-current="page" @endif>
                <i class="bi bi-list-ul"></i> Log isian
            </a>

            {{-- Halaman hasil adalah tampilan videotron layar penuh, dibuka di tab baru
                 supaya panel tidak hilang saat ditampilkan ke layar. --}}
            <a href="{{ route('admin.result.index') }}" target="_blank" rel="noopener" class="adm-nav-link">
                <i class="bi bi-bar-chart"></i> Hasil
                <i class="bi bi-box-arrow-up-right adm-nav-ext"></i>
            </a>

            <a href="{{ route('admin.setting.edit') }}"
               class="adm-nav-link {{ request()->routeIs('admin.setting.*') ? 'is-active' : '' }}"
               @if(request()->routeIs('admin.setting.*')) aria-current="page" @endif>
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        </nav>

        <div class="adm-sidebar-footer">
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="adm-logout">
                    <i class="bi bi-box-arrow-left"></i> Logout
                </button>
            </form>
        </div>
    </div>
</aside>
