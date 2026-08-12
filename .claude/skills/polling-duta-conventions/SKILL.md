---
name: polling-duta-conventions
description: Konvensi wajib untuk proyek web registrasi ulang dan polling duta berbasis Laravel 13 di shared hosting. Gunakan skill ini setiap kali menulis, mengubah, atau mereview kode apapun di proyek ini — termasuk migration, controller, Blade, routing, validasi, dan konfigurasi deploy. Wajib dipakai juga ketika diminta menambahkan styling, komponen UI, autentikasi, upload file, atau apapun yang menyentuh asset frontend, karena proyek ini punya batasan hosting yang tidak biasa dan solusi Laravel standar sering justru merusaknya.
---

# Konvensi Proyek Polling Duta

Proyek Laravel 13 untuk acara pemilihan duta: registrasi ulang plus voting kandidat, dijalankan di shared hosting tanpa proses build.

Skala kecil dan sekali jalan — di bawah 500 voter, sekitar 10 kandidat. Kalau ada dua pendekatan dan satu punya lebih sedikit bagian bergerak, pilih yang itu. Keandalan di hari acara lebih berharga daripada arsitektur yang rapi.

## Batasan yang tidak bisa ditawar

Shared hosting tidak bisa menjalankan proses build, jadi seluruh pipeline asset harus dihindari:

- Jangan pernah menulis `@vite(...)` di Blade
- Jangan membuat `package.json`, `vite.config.js`, atau menyarankan `npm install` / `npm run build`
- Bootstrap 5 dimuat lewat CDN di layout, bukan file lokal atau hasil kompilasi
- JavaScript ditulis vanilla, langsung di Blade atau file `.js` statis di `public/`

Autentikasi juga menyimpang dari default Laravel:

- Tidak ada tabel `users`, tidak ada Breeze, Jetstream, atau Fortify
- Panitia login dengan satu password bersama, hash-nya di tabel `settings`
- Middleware admin cukup mengecek satu flag di session

Frontend stack lain yang tidak dipakai: Livewire, Inertia, Vue, React. Blade saja.

## Styling

Pakai komponen bawaan Bootstrap sebelum menulis CSS sendiri. Card, grid, form-control, alert, table, dan progress sudah menutupi hampir semua kebutuhan proyek ini. CSS custom hanya untuk hal yang benar-benar tidak ada komponennya — misalnya membuat container foto berasio tetap dengan `object-fit: cover`.

Alasan proyek ini memilih Bootstrap adalah supaya tidak perlu menyusun tampilan dari nol. Menawarkan CSS custom untuk sesuatu yang sudah ada komponennya membatalkan alasan itu.

## Skema database

Tiga tabel: `candidates`, `votes`, `settings`.

`votes` menyimpan `nama` dan `candidate_id` dalam satu baris. **Jangan usulkan memecahnya menjadi tabel `voters` terpisah.** Registrasi dan voting terjadi dalam satu submit, jadi tidak akan pernah ada registrasi tanpa vote — memisahkannya hanya menambah join tanpa manfaat.

**Voter tidak diidentifikasi sama sekali.** Kolom `no_hp` sudah dihapus atas permintaan pemilik proyek, jadi tidak ada pencegahan suara ganda — tidak ada login voter, cookie, maupun session. Jangan menambahkan kembali kolom identitas, unique constraint, atau pengecekan duplikat kecuali diminta.

Konfigurasi aplikasi tinggal di tabel `settings`, bukan `.env`. Pengecualiannya hanya `APP_KEY` dan kredensial database, karena keduanya dibutuhkan sebelum koneksi database terbentuk.

## Halaman voter: satu halaman, satu submit

Kedua step ada di DOM sejak awal. Step 2 disembunyikan, lalu ditampilkan lewat JavaScript setelah step 1 valid. Semuanya dikirim dalam satu POST.

Jangan usulkan session multi-step atau token di URL. Device disediakan panitia dan dipakai bergantian oleh banyak orang — dengan satu halaman, redirect setelah submit sudah cukup untuk mereset device bagi voter berikutnya, tanpa perlu menghapus session secara manual.

Pasang header no-cache di halaman form supaya tombol back tidak memunculkan data voter sebelumnya.

Voter adalah masyarakat umum yang mengantre. Tombol besar, teks jelas, langkah sesedikit mungkin.

## Kandidat tidak bisa dihapus

Jangan buat tombol hapus, route destroy, atau method delete untuk kandidat. Menghapus kandidat yang sudah menerima suara merusak perhitungan hasil. Yang tersedia hanya toggle `is_active`.

## Upload foto

Foto sudah di-resize manual oleh pengelola sebelum diupload, jadi jangan menyarankan Intervention Image atau library pemroses gambar lain. Cukup validasi di Form Request: image, jpeg, maksimal 2MB.

Standar yang dipakai: 600 × 800 px, JPEG, sekitar 100–150KB.

Simpan ke `storage/app/public/kandidat/`.

## Halaman hasil: tanpa live update

Hasil baru ditayangkan setelah voting ditutup, jadi halaman cukup merender hitungan saat dimuat. Jangan tambahkan AJAX polling, endpoint JSON, atau `setInterval`.

Route-nya publik tanpa autentikasi — link tidak disebarkan, dan operator videotron perlu membukanya tanpa login.

Rasio layar videotron belum ditentukan. Gunakan satuan relatif (`vw`, `vh`) untuk ukuran teks, hindari piksel tetap.

## Deployment

Deploy lewat `git clone` ke shared hosting, subdomain diarahkan ke folder `public/`. Composer tersedia di server, jadi `vendor/` tidak perlu di-commit.

Symlink storage dibuat manual dengan `ln -s`, bukan `php artisan storage:link`. Artisan memanggil fungsi `symlink()` milik PHP yang biasanya dimatikan lewat `disable_functions` di shared hosting; `ln -s` berjalan di level shell sehingga lolos.

Pastikan `.gitignore` memuat `/vendor`, `/public/storage`, `/storage/app/public/kandidat`, dan `.env`. Kalau `public/storage` ikut ter-commit, hasil clone di server jadi symlink rusak.

## Kalau ragu

Ketika sebuah solusi Laravel standar bertabrakan dengan batasan di atas, batasan yang menang — dan sebutkan tabrakan itu ke pengguna alih-alih diam-diam mencari jalan pintas. Contohnya, kalau sebuah fitur biasanya butuh proses build, katakan terus terang bahwa fitur itu tidak cocok untuk proyek ini, jangan tawarkan versi yang "hampir jalan".
