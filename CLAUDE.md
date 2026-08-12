# Polling Duta

Baca `SETUP-CLAUDE-CODE.md` sebelum mengerjakan apapun di proyek ini.

Proyek ini sengaja menyimpang dari beberapa default Laravel karena batasan shared hosting. **Kalau aturan di sini bertabrakan dengan `<laravel-boost-guidelines>` di bawah, aturan di sini yang menang.**

## Pengecualian terhadap guideline Boost

**Tidak ada frontend bundling.** Proyek ini tidak punya `package.json`, `vite.config.js`, maupun `node_modules`. Abaikan seluruh bagian "Frontend Bundling" dan "Vite Error" di bawah. Jangan pernah menyarankan `npm run build`, `npm run dev`, atau `composer run dev`. Jangan menulis `@vite(...)` di Blade.

Kalau perubahan tampilan tidak muncul, penyebabnya cache Blade atau cache browser — bukan bundler. Sarankan `php artisan view:clear`, bukan npm.

**Bukan Laravel Cloud.** Abaikan bagian "Deployment" di bawah. Deploy proyek ini lewat `git clone` ke shared hosting; prosedurnya ada di `SETUP-CLAUDE-CODE.md`.

**Folder `app/Support/` disetujui.** Guideline melarang membuat folder baru tanpa persetujuan — folder ini sudah termasuk rancangan dan boleh dibuat.

## Aturan inti proyek

- Bootstrap 5 lewat CDN. Utamakan komponen bawaan Bootstrap sebelum menulis CSS sendiri.
- JavaScript vanilla, ditaruh di `public/js/`, bukan `resources/js/`.
- Tanpa Livewire, Inertia, Vue, React.
- Tanpa tabel `users`, tanpa Breeze/Jetstream/Fortify. Login panitia memakai satu password bersama.
- Konfigurasi aplikasi di tabel `settings`, bukan `.env`. Pengecualian: `APP_KEY` dan kredensial database.
- Kandidat tidak bisa dihapus — hanya toggle `is_active`.

Detail lengkap dan alasan di balik tiap aturan ada di `SETUP-CLAUDE-CODE.md`.

---