<?php

namespace App\Http\Controllers;

use App\Models\ProfilDesa;
use App\Models\StatistikDesa;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Carousel;
use App\Models\Pengaturan;
use App\Core\Database;

class HomeController extends Controller
{
    public function index()
    {
        $profil = ProfilDesa::first() ?: [];
        $statistik = StatistikDesa::all() ?: [];
        $berita = Berita::latest(3) ?: [];
        $galeri = Galeri::latest(5) ?: [];
        $carousel = Carousel::active() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];

        $desaNama = $profil['nama_desa'] ?? ($pengaturan['website_nama'] ?? 'Desa');
        $alamatDesa = $profil['alamat'] ?? '';
        $teleponDesa = $profil['telepon'] ?? '';
        $emailDesa = $profil['email'] ?? '';

        $sejarahRaw = trim(strip_tags((string) ($profil['sejarah'] ?? '')));
        $sejarahSingkat = $sejarahRaw !== '' ? mb_substr($sejarahRaw, 0, 220) . (mb_strlen($sejarahRaw) > 220 ? '...' : '') : '';

        $visiMisiRaw = trim(strip_tags((string) ($profil['visi_misi'] ?? '')));
        $visi = '';
        $misiList = [];

        if ($visiMisiRaw !== '') {
            if (preg_match('/visi\s*[:\-]\s*(.+?)(?=\bmisi\b\s*[:\-]|$)/is', $visiMisiRaw, $matchVisi)) {
                $visi = trim($matchVisi[1]);
            }
            if (preg_match('/misi\s*[:\-]\s*(.+)$/is', $visiMisiRaw, $matchMisi)) {
                $rawMisi = preg_split('/\r\n|\r|\n|\d+[\.)]\s*/', trim($matchMisi[1]));
                foreach ($rawMisi as $item) {
                    $item = trim($item, " \t\n\r\0\x0B-•");
                    if ($item !== '') {
                        $misiList[] = $item;
                    }
                }
            }
        }

        if ($visi === '' && !empty($misiList)) {
            $visi = $misiList[0];
        }

        $pickStat = static function (array $rows, array $keywords, string $defaultValue): string {
            foreach ($rows as $row) {
                $name = strtolower((string) ($row['nama_statistik'] ?? ''));
                foreach ($keywords as $keyword) {
                    if (str_contains($name, $keyword)) {
                        return (string) ($row['nilai_statistik'] ?? $defaultValue);
                    }
                }
            }
            return $defaultValue;
        };

        $totalPenduduk = $pickStat($statistik, ['penduduk', 'jiwa'], '0');
        $luasWilayah = $pickStat($statistik, ['luas', 'wilayah', 'ha', 'km'], '0');
        $totalKk = $pickStat($statistik, ['keluarga', 'kk'], '0');

        $totalBeritaViews = 0;
        $totalGaleriViews = 0;
        try {
            $beritaViewsRow = Database::getInstance()->getConnection()->query('SELECT COALESCE(SUM(views), 0) AS total FROM berita')->fetch();
            $totalBeritaViews = (int) ($beritaViewsRow['total'] ?? 0);
        } catch (\Throwable $e) {
            $totalBeritaViews = 0;
        }
        try {
            $galeriViewsRow = Database::getInstance()->getConnection()->query('SELECT COALESCE(SUM(views), 0) AS total FROM galeri')->fetch();
            $totalGaleriViews = (int) ($galeriViewsRow['total'] ?? 0);
        } catch (\Throwable $e) {
            $totalGaleriViews = 0;
        }
        $totalLandingViews = $totalBeritaViews + $totalGaleriViews;

        $misi1 = $misiList[0] ?? '';
        $misi2 = $misiList[1] ?? '';
        $misi3 = $misiList[2] ?? '';
        $misi4 = $misiList[3] ?? '';

        $logoDesa = (string) ($pengaturan['logo_desa'] ?? '');
        if ($logoDesa === '') {
            try {
                $logoRow = Database::getInstance()->table('galeri')
                    ->where('kategori', 'Logo')
                    ->orderBy('created_at', 'DESC')
                    ->first();
                if (!empty($logoRow['gambar'])) {
                    $logoDesa = 'galeri/' . ltrim((string) $logoRow['gambar'], '/');
                }
            } catch (\Throwable $e) {
                $logoDesa = '';
            }
        }

        $data = [
            'title' => 'Beranda - ' . ($desaNama ?: 'Website Desa'),
            'page' => 'home',
            'profil' => $profil,
            'statistik' => $statistik,
            'berita' => $berita,
            'galeri' => $galeri,
            'carousel' => $carousel,
            'pengaturan' => $pengaturan,
            'desa_nama' => $desaNama,
            'desa_alamat' => $alamatDesa,
            'desa_telepon' => $teleponDesa,
            'desa_email' => $emailDesa,
            'alamatDesa' => $alamatDesa,
            'teleponDesa' => $teleponDesa,
            'emailDesa' => $emailDesa,
            'website_nama' => $pengaturan['website_nama'] ?? 'Website Desa',
            'website_deskripsi' => $pengaturan['website_deskripsi'] ?? '',
            'social_facebook' => $pengaturan['social_facebook'] ?? '',
            'social_instagram' => $pengaturan['social_instagram'] ?? '',
            'social_youtube' => $pengaturan['social_youtube'] ?? '',
            'whatsapp_number' => $pengaturan['whatsapp_number'] ?? '',
            'logoDesa' => $logoDesa,
            'sejarah' => $sejarahRaw,
            'sejarahSingkat' => $sejarahSingkat,
            'visi' => $visi,
            'misiList' => $misiList,
            'misi1' => $misi1,
            'misi2' => $misi2,
            'misi3' => $misi3,
            'misi4' => $misi4,
            'totalPenduduk' => $totalPenduduk,
            'luasWilayah' => $luasWilayah,
            'totalKk' => $totalKk,
            'totalBeritaViews' => $totalBeritaViews,
            'totalGaleriViews' => $totalGaleriViews,
            'totalLandingViews' => $totalLandingViews,
        ];

        return $this->view('pages/home/index', $data);
    }
}
