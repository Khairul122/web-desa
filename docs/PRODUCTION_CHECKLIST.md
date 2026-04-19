# Production Checklist

Checklist ini membantu menyiapkan `website_desa` ke mode production dengan aman.

## 1) Konfigurasi Environment

- Pastikan `APP_ENV` untuk server production bernilai `production`.
- Isi `config/secrets.production.php` dengan data nyata server production:
  - `BASE_URL` (contoh: `https://domain-anda.com`)
  - `DB_HOST`
  - `DB_HOSTS` (opsional, dipisah koma)
  - `DB_NAME`
  - `DB_USER`
  - `DB_PASS`
- Jangan simpan kredensial production di `config/secrets.php`.

## 2) Web Server (Apache)

- Aktifkan `mod_rewrite`.
- Pastikan `AllowOverride All` pada DocumentRoot proyek agar `.htaccess` dipakai.
- Untuk hosting di subfolder, sesuaikan `RewriteBase` di `.htaccess`.
- Verifikasi route utama tidak 404:
  - `/`
  - `/login`
  - `/berita`
  - `/galeri`
  - `/kontak`

## 3) Database

- Buat database production terpisah dari development.
- Import skema awal dari `database/website_desa.sql` jika server baru.
- Pastikan user DB hanya punya hak minimal yang diperlukan.
- Uji koneksi DB dari aplikasi (tanpa error 500).

## 4) Permissions & Runtime Folder

- Pastikan folder writable untuk runtime:
  - `storage/logs`
  - `uploads/logo`
  - `uploads/artikel`
  - `uploads/carousel`
  - `uploads/galeri`
  - `uploads/editor`
- Pastikan file log error dapat dibuat di `storage/logs/errors.log`.

## 5) Keamanan Dasar

- `display_errors` harus off di production (sudah diatur saat `APP_ENV=production`).
- Gunakan HTTPS dan sertifikat valid.
- Rotasi password DB jika sempat terekspos.
- Pastikan tidak ada file rahasia ikut ter-publish.

## 6) Verifikasi Fungsional

- Cek login admin berhasil.
- Cek CRUD berita minimal 1 siklus (buat, edit, hapus).
- Cek form kontak bisa submit.
- Cek upload gambar dari admin berjalan.
- Cek tampilan mobile dan desktop untuk halaman publik utama.

## 7) Backup & Operasional

- Siapkan backup berkala:
  - database
  - folder `uploads/`
- Simpan prosedur restore sederhana.
- Pantau `storage/logs/errors.log` setelah rilis pertama.
