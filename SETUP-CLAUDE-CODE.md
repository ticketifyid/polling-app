# Setup Claude Code — Web Registrasi Ulang & Polling Duta

Dokumen ini adalah sumber kebenaran untuk proyek. Baca seluruhnya sebelum menulis kode apapun.

---

## 1. Ringkasan

Web sederhana untuk acara pemilihan duta. Peserta melakukan registrasi ulang lalu memilih satu kandidat. Panitia mengelola daftar kandidat dan menayangkan hasilnya di videotron setelah voting ditutup.

**Skala:** di bawah 500 voter, sekitar 10 kandidat, acara sekali jalan.

**Karakter proyek:** ini bukan aplikasi jangka panjang. Prioritaskan keandalan di hari-H di atas kerapian arsitektur. Kalau ada dua pilihan dan satu lebih sedikit bagian bergeraknya, pilih yang itu.

---

## 2. Stack

| Komponen  | Pilihan                                |
| --------- | -------------------------------------- |
| Framework | Laravel 13                             |
| PHP       | 8.3                                    |
| Database  | MySQL                                  |
| CSS       | Bootstrap 5 via CDN                    |
| JS        | Vanilla JS (tanpa framework)           |
| Hosting   | Shared hosting, deploy via `git clone` |

---

## 3. Batasan keras

Ini bukan preferensi. Melanggarnya membuat aplikasi gagal deploy.

- **Tanpa proses build.** Tidak ada Vite, tidak ada npm, tidak ada `package.json`. Jangan pernah menulis `@vite(...)` di Blade.
- **Bootstrap lewat CDN**, bukan file lokal, bukan hasil kompilasi:
    ```html
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />
    ```
- **Utamakan komponen bawaan Bootstrap.** Card, grid, form-control, alert, table, progress sudah tersedia. Tulis CSS custom hanya kalau memang tidak ada komponennya.
- **Tanpa Breeze, Jetstream, Fortify, atau tabel `users`.** Autentikasi panitia memakai satu password bersama.
- **Tanpa Livewire, Inertia, Vue, atau React.** Blade + vanilla JS saja.
- **Konfigurasi aplikasi disimpan di tabel `settings`,** bukan di `.env`. Pengecualian: `APP_KEY` dan kredensial database tetap wajib di `.env` karena dibutuhkan sebelum koneksi database terbentuk.

---

## 4. Skema database

Tiga tabel. Tidak ada tabel `voters` terpisah — nama voter disimpan langsung di baris vote, karena registrasi dan voting terjadi dalam satu submit.

### `candidates`

| Kolom      | Tipe      | Catatan                |
| ---------- | --------- | ---------------------- |
| id         | bigint PK |                        |
| nama       | string    |                        |
| foto       | string    | path relatif file      |
| urutan     | integer   | mengatur posisi tampil |
| is_active  | boolean   | default true           |
| timestamps |           |                        |

### `votes`

| Kolom        | Tipe            | Catatan             |
| ------------ | --------------- | ------------------- |
| id           | bigint PK       |                     |
| nama         | string          |                     |
| candidate_id | FK → candidates |                     |
| created_at   | timestamp       |                     |

**Tidak ada pencegahan suara ganda.** Kolom `no_hp` beserta constraint `unique`-nya sudah dihapus atas permintaan pemilik proyek. Satu orang bisa mengisi berkali-kali, dan sistem tidak punya cara membedakannya — tidak ada login voter, cookie, maupun session. Kendali sepenuhnya ada di panitia yang menjaga device. Jangan menambahkan kembali kolom identitas apa pun tanpa diminta.

### `settings`

| Kolom | Tipe      |
| ----- | --------- |
| key   | string PK |
| value | text      |

Isi awal: `nama_acara`, `polling_dibuka` (boolean), `admin_password` (hash).

---

## 5. Halaman voter

**Satu halaman, satu submit.** Bukan dua request terpisah, bukan session multi-step.

Struktur:

- Step 1 (nama) dan Step 2 (grid kandidat) sama-sama ada di DOM sejak awal.
- Step 2 tersembunyi. Tombol "Lanjut" memvalidasi step 1 di sisi klien, lalu menyembunyikan step 1 dan menampilkan step 2.
- Tombol "Kirim" mengirim semuanya dalam satu POST.

**Alasan desain ini:** device disediakan panitia dan dipakai bergantian oleh banyak orang. Dengan satu halaman, setelah submit cukup redirect ke halaman kosong dan device otomatis siap untuk voter berikutnya — tanpa perlu menghapus session atau cookie secara manual.

### Setelah submit berhasil

- Tampilkan halaman konfirmasi singkat.
- Sediakan tombol besar "Voter Berikutnya" yang mengarah kembali ke form kosong.
- Pasang header no-cache di halaman form, supaya tombol back tidak menampilkan data voter sebelumnya dari cache browser.

### Ketika polling ditutup

Kalau `settings.polling_dibuka` bernilai false, halaman form diganti pesan "Voting telah ditutup". Jangan tampilkan formnya sama sekali.

### Catatan UI

Voter adalah masyarakat umum. Tombol besar, teks jelas, langkah minimal. Foto kandidat ditampilkan dalam Bootstrap card dengan rasio seragam — gunakan `object-fit: cover` pada container berasio tetap supaya foto dengan dimensi berbeda tetap terlihat rapi.

---

## 6. Halaman admin

Semua di bawah prefix `/admin`, dilindungi middleware yang mengecek satu flag session. Login memakai satu password bersama yang hash-nya disimpan di tabel `settings`.

| Route             | Fungsi                         |
| ----------------- | ------------------------------ |
| `/admin/login`    | Form password tunggal          |
| `/admin/kandidat` | Tambah, edit, dan toggle aktif |
| `/admin/log`      | Tabel semua isian voter        |
| `/admin/setting`  | Buka/tutup polling, nama acara |

### Kandidat

**Tidak ada fitur hapus sama sekali.** Jangan buat tombol hapus, jangan buat route destroy. Menghapus kandidat yang sudah punya suara akan merusak perhitungan hasil. Yang tersedia hanya toggle `is_active` — kandidat nonaktif hilang dari halaman voter tapi datanya utuh.

### Upload foto

Foto disiapkan dan di-resize manual oleh pengelola sebelum diupload, jadi **tidak perlu library pemroses gambar** (Intervention Image dan sejenisnya). Cukup validasi di Form Request:

```php
'foto' => 'required|image|mimes:jpeg,jpg|max:2048',
```

Standar foto yang dipakai: **600 × 800 px, JPEG, quality 80–85, sekitar 100–150KB.**

Simpan ke `storage/app/public/kandidat/`. Akses publik lewat symlink (lihat bagian deployment).

### Log isian

Tabel berisi nama, kandidat pilihan, dan waktu. Sediakan paginasi dan pencarian sederhana. Halaman ini murni baca — tidak ada edit atau hapus.

### Setting

Toggle buka/tutup polling wajib ada dan mudah ditemukan. Panitia memakainya untuk menutup voting sebelum hasil diumumkan.

---

## 7. Halaman hasil

Berada di balik middleware `admin`. Operator videotron login sekali di laptopnya, lalu session bertahan sepanjang acara.

Karena itu `SESSION_LIFETIME` harus dipanjangkan di `.env` dan `.env.example`:

```env
SESSION_LIFETIME=600
```

Default Laravel 120 menit terlalu pendek. Kalau operator login jauh sebelum pengumuman, session bisa kedaluwarsa persis saat halaman perlu di-refresh setelah polling ditutup.

**Tanpa live update.** Tidak ada AJAX polling, tidak ada endpoint JSON, tidak ada `setInterval`. Hasil baru ditayangkan setelah seluruh voting selesai, jadi halaman cukup merender hitungan saat dimuat. Konsekuensinya, halaman harus di-refresh setelah polling ditutup — angka yang tampil adalah angka saat halaman terakhir dimuat.

Tampilan:

- Foto dan nama tiap kandidat beserta jumlah suara.
- Urut dari perolehan terbanyak.
- Rasio layar videotron belum ditentukan, jadi gunakan satuan relatif (`vw`, `vh`) agar teks ikut menyesuaikan ukuran layar. Hindari ukuran piksel tetap.
- Tanpa navbar, tanpa menu — hanya konten hasil.

Query-nya cukup satu agregasi:

```php
Candidate::withCount('votes')->orderByDesc('votes_count')->get();
```

---

## 8. Struktur folder

Tanda `←` menandai file yang dibuat khusus untuk proyek ini. Sisanya bawaan Laravel.

```
polling-duta/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── VoteController.php          ← form voter + simpan vote
│   │   │   ├── ResultController.php        ← halaman videotron
│   │   │   └── Admin/
│   │   │       ├── AuthController.php      ← login password tunggal
│   │   │       ├── CandidateController.php ← CRUD kandidat (tanpa destroy)
│   │   │       ├── LogController.php       ← tabel isian voter
│   │   │       └── SettingController.php   ← buka/tutup polling
│   │   ├── Middleware/
│   │   │   └── AdminAuth.php               ← cek flag session
│   │   └── Requests/
│   │       ├── StoreVoteRequest.php
│   │       └── StoreCandidateRequest.php
│   ├── Models/
│   │   ├── Candidate.php
│   │   ├── Vote.php
│   │   └── Setting.php
│   └── Support/
│
├── bootstrap/
│   └── app.php                             ← alias middleware didaftarkan di sini
│
├── database/
│   ├── migrations/
│   │   ├── ..._create_candidates_table.php
│   │   ├── ..._create_votes_table.php
│   │   └── ..._create_settings_table.php
│   └── seeders/
│       └── SettingSeeder.php               ← isi awal setting + password admin
│
├── public/
│   ├── index.php                           ← subdomain diarahkan ke folder ini
│   ├── .htaccess
│   ├── js/
│   │   └── vote.js                         ← toggle step 1 → step 2
│   └── storage/                            ← symlink, jangan di-commit
│
├── resources/views/
│   ├── layouts/
│   │   ├── app.blade.php                   ← layout umum + CDN Bootstrap
│   │   └── videotron.blade.php             ← layout polos untuk hasil
│   ├── vote/
│   │   ├── index.blade.php                 ← satu halaman, dua step
│   │   ├── success.blade.php
│   │   └── closed.blade.php
│   ├── result/
│   │   └── index.blade.php
│   └── admin/
│       ├── login.blade.php
│       ├── log.blade.php
│       ├── setting.blade.php
│       └── candidates/
│           ├── index.blade.php
│           ├── create.blade.php
│           └── edit.blade.php
│
├── routes/
│   └── web.php
│
├── storage/app/public/kandidat/            ← foto kandidat tersimpan di sini
│
├── .env                                    ← hanya APP_KEY + kredensial DB
└── .gitignore
```

### Middleware didaftarkan di `bootstrap/app.php`

Sejak Laravel 11 file `app/Http/Kernel.php` sudah tidak ada. Alias middleware admin didaftarkan seperti ini:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['admin' => \App\Http\Middleware\AdminAuth::class]);
})
```

Banyak tutorial di internet masih memakai struktur Laravel 10, jadi bagian ini rawan salah.

### JavaScript di `public/`, bukan `resources/js/`

Isi `resources/js/` normalnya diproses Vite. Karena proyek ini tanpa build, JS ditaruh langsung di `public/js/` dan dipanggil dengan `asset()`. File di `public/` disajikan apa adanya oleh web server.

---

## 9. Deployment

Subdomain diarahkan langsung ke folder `public/`.

```bash
git clone <repo> ~/polling
cd ~/polling
composer install --no-dev --optimize-autoloader
cp .env.example .env          # isi APP_KEY dan kredensial DB
php artisan key:generate
php artisan migrate --seed
ln -s ~/polling/storage/app/public ~/polling/public/storage
php artisan config:cache
```

### Kenapa symlink dibuat manual

`php artisan storage:link` memanggil fungsi `symlink()` milik PHP, yang di banyak shared hosting dimatikan lewat `disable_functions`. Perintah `ln -s` berjalan di level shell sehingga tidak terkena pembatasan itu.

### `.gitignore`

Pastikan berisi minimal:

```
/vendor
/public/storage
/storage/app/public/kandidat
.env
```

`public/storage` wajib diabaikan. Kalau ikut ter-commit, hasil clone di server akan berupa symlink rusak yang menunjuk ke path laptop.

---

## 10. Setup Claude Code

### Laravel Boost — pasang lebih dulu

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Boost adalah MCP server resmi Laravel. Ia memberi Claude Code akses ke struktur aplikasi, skema database, daftar route, dan dokumentasi Laravel yang sesuai versi. Tanpa ini, Claude menebak-nebak versi dan struktur proyek. Karena dipasang sebagai `--dev`, ia tidak ikut ke server produksi.

### Skill

```
/plugin marketplace add laravel/agent-skills
/plugin install laravel@laravel
```

Memberi agent `laravel-simplifier` untuk merapikan kode sesuai konvensi Laravel dan PSR-12.

Tambahkan juga skill custom proyek ini (lihat `SKILL.md`) ke `.claude/skills/` di root proyek.

### Skill yang sebaiknya TIDAK dipasang

`frontend-design`, `theme-factory`, `brand-guidelines`, dan `canvas-design` ditulis dengan asumsi CSS utility-first. Kalau dipasang bersama instruksi "pakai Bootstrap", Claude cenderung menawarkan CSS custom ketimbang memakai komponen Bootstrap yang sudah ada — kebalikan dari tujuan memilih Bootstrap. Empat skill itu juga saling tumpang tindih domainnya, yang bisa menghasilkan arahan estetik yang tarik-menarik.

---

## 11. Checklist sebelum hari-H

- [ ] Ekstensi PHP di hosting sudah memenuhi syarat Laravel 13
- [ ] Symlink `public/storage` berfungsi — cek dengan membuka satu foto kandidat lewat browser
- [ ] Semua foto kandidat sudah diupload dan tampil rapi di grid
- [ ] Uji tombol back setelah submit — tidak boleh menampilkan data voter sebelumnya
- [ ] Uji toggle tutup polling — form voter harus berganti jadi pesan penutupan
- [ ] Halaman hasil sudah dicoba di layar dengan rasio berbeda
- [ ] Device acara sudah disiapkan: browser terbuka di halaman form, zoom sesuai, layar tidak auto-sleep
