# Website Resmi Gampong Meunye Pirak

Portal informasi dan layanan administrasi digital Gampong Meunye Pirak, Kabupaten Aceh Utara, Provinsi Aceh. Proyek dibangun dengan PHP native berarsitektur MVC, memiliki portal publik dan panel admin, serta mendukung deploy production ke InfinityFree melalui GitHub Actions.

## Fitur Utama

- Portal publik: beranda, profil, visi-misi, struktur organisasi, berita, galeri, kontak.
- Panel admin: manajemen berita, galeri, carousel, statistik, pesan kontak, profil, pengaturan website.
- Upload media: logo website, thumbnail berita, gambar carousel, media galeri, gambar editor.
- SEO dasar: metadata, Open Graph, canonical URL, dan structured data pada halaman relevan.
- Deploy otomatis ke InfinityFree via GitHub Actions (FTP).

## Stack

- PHP 8.1+
- MySQL / MariaDB
- Apache + mod_rewrite
- Bootstrap 5 + Bootstrap Icons
- Vanilla JavaScript

## Struktur Penting

- `app/` source aplikasi (controllers, models, views)
- `core/` mini framework internal (router, request, response, database, view)
- `routes/web.php` daftar route aplikasi
- `config/app.php` konfigurasi aplikasi (env-aware)
- `config/database.php` konfigurasi database (env-aware)
- `config/secrets.example.php` template secret lokal/production
- `database/website_desa.sql` skema + data awal
- `database/seeders/pengaturan_munye_pirak.sql` data pengaturan awal Meunye Pirak
- `.github/workflows/deploy-infinityfree.yml` workflow deploy

## Setup Lokal

1. Clone repository ke web root (contoh Laragon: `C:/laragon/www/web-desa`).
2. Buat database baru.
3. Import `database/website_desa.sql`.
4. Gunakan salah satu mode konfigurasi:
    - Mode cepat: salin `config/secrets.example.php` menjadi `config/secrets.php`.
    - Mode multi-env: gunakan `config/secrets.development.php` dan `config/secrets.production.php`.
5. Isi nilai sesuai environment (`APP_ENV`, `BASE_URL`, `DB_*`).
6. Untuk mode localhost berbasis subfolder, gunakan URL `http://localhost/web-desa`.
7. Jalankan aplikasi dari web server lokal.

Contoh `config/secrets.php` lokal:

```php
<?php

return [
    'APP_ENV' => 'development',
    'BASE_URL' => 'http://localhost/web-desa',
    'LOG_VIEWER_TOKEN' => '',
    'DB_HOST' => 'localhost',
    'DB_HOSTS' => 'localhost',
    'DB_NAME' => 'website_desa',
    'DB_USER' => 'root',
    'DB_PASS' => '',
];
```

## Deploy ke InfinityFree

Deploy menggunakan GitHub Actions dengan workflow:

- `.github/workflows/deploy-infinityfree.yml`

### GitHub Secrets Wajib

- `INFINITYFREE_FTP_SERVER` (atau `FTP_SERVER`)
- `INFINITYFREE_FTP_USERNAME` (atau `FTP_USERNAME`)
- `INFINITYFREE_FTP_PASSWORD` (atau `FTP_PASSWORD`)
- `INFINITYFREE_FTP_REMOTE_DIR` (atau `FTP_REMOTE_DIR`)
- `APP_BASE_URL`
- `APP_DB_HOST`
- `APP_DB_NAME`
- `APP_DB_USER`
- `APP_DB_PASS`

### GitHub Secrets Opsional

- `APP_ENV` (default `production`)
- `APP_DB_HOSTS` (fallback host dipisah koma)
- `APP_LOG_VIEWER_TOKEN` (disarankan kosong di production)

Workflow akan membuat `config/secrets.php` otomatis saat deploy, lalu upload source code dan folder `uploads/` via FTP.

## Dua Mode Environment (Development & Production)

Project mendukung dua file secret terpisah:

- `config/secrets.development.php`
- `config/secrets.production.php`

Pemilihan file dilakukan otomatis berdasarkan nilai `APP_ENV` runtime:

- `APP_ENV=development` -> pakai `config/secrets.development.php`
- `APP_ENV=production` -> pakai `config/secrets.production.php`
- Jika file sesuai environment tidak ada, fallback ke `config/secrets.php`

Default development yang disiapkan saat ini:

```php
'BASE_URL' => 'http://localhost/web-desa',
'DB_HOST' => 'localhost',
'DB_NAME' => 'website_desa',
'DB_USER' => 'root',
'DB_PASS' => '',
```

## Kebijakan Upload Gambar

- Upload gambar dari panel admin disimpan pada folder `uploads/*`.
- Workflow deploy saat ini ikut mengirim folder `uploads/` ke InfinityFree, sehingga media lokal ikut tersinkron saat deploy.
- Folder uploads yang dipakai aplikasi:
  - `uploads/logo`
  - `uploads/artikel`
  - `uploads/carousel`
  - `uploads/galeri`
  - `uploads/editor`
- Jika Anda mengubah isi media di localhost, file tersebut akan ikut ter-publish pada deploy berikutnya.
- Jika ada upload baru langsung di server production, pastikan file itu juga dibackup ke lokal agar tidak tertimpa saat deploy selanjutnya.

## Catatan Path Media

- Hero section membaca gambar carousel dari folder `uploads/carousel`.
- Thumbnail berita membaca gambar dari folder `uploads/artikel`.
- Galeri membaca gambar dari folder `uploads/galeri`.
- Logo website dibaca dari `uploads/logo` atau `public/uploads/logo` dengan fallback resolver otomatis.

## Backup Rekomendasi

- Backup database minimal mingguan (phpMyAdmin export SQL).
- Backup folder `uploads/` minimal mingguan (FTP download).
- Lakukan backup sebelum deploy besar/perubahan skema.

## Keamanan

- Jangan commit file `config/secrets.php`.
- Jangan simpan token/kredensial di source code.
- Gunakan mode production (`APP_ENV=production`) saat live.
- Jika kredensial pernah terekspos, lakukan rotate segera.

## Dokumen Tambahan

- `docs/PRODUCTION_CHECKLIST.md`
- `docs/OPERASIONAL_UPLOAD_DAN_BACKUP.md`
- `docs/PRODUCTION_ENV_TEMPLATE.md`

## Status Deploy Saat Ini

- Mode runtime production aktif pada entry file aplikasi.
- Workflow GitHub Actions mengirim source code beserta `uploads/` ke InfinityFree.
- Target production saat ini: `https://munyepirak.wuaze.com`
