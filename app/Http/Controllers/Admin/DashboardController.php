<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];
        $db = Database::getInstance();

        $safeCount = static function (callable $callback): int {
            try {
                return (int) $callback();
            } catch (\Throwable $e) {
                return 0;
            }
        };

        $metrics = [
            'berita_publish' => $safeCount(static fn() => $db->table('berita')->where('status', 'publish')->count()),
            'berita_draft' => $safeCount(static fn() => $db->table('berita')->where('status', 'draft')->count()),
            'galeri_total' => $safeCount(static fn() => $db->table('galeri')->count()),
            'pesan_baru' => $safeCount(static fn() => $db->table('kontak')->where('status', 'baru')->count()),
            'user_aktif' => $safeCount(static fn() => $db->table('users')->where('is_active', 1)->count()),
            'berita_views' => 0,
            'galeri_views' => 0,
            'total_views' => 0,
        ];

        try {
            $viewsRow = $db->getConnection()->query('SELECT COALESCE(SUM(views), 0) AS total FROM berita')->fetch();
            $metrics['berita_views'] = (int) ($viewsRow['total'] ?? 0);
        } catch (\Throwable $e) {
            $metrics['berita_views'] = 0;
        }

        try {
            $viewsRow = $db->getConnection()->query('SELECT COALESCE(SUM(views), 0) AS total FROM galeri')->fetch();
            $metrics['galeri_views'] = (int) ($viewsRow['total'] ?? 0);
        } catch (\Throwable $e) {
            $metrics['galeri_views'] = 0;
        }
        $metrics['total_views'] = $metrics['berita_views'] + $metrics['galeri_views'];

        $beritaTerbaru = [];
        $pesanTerbaru = [];
        $periodViews = [];
        $topContentViews = [];

        try {
            $beritaTerbaru = $db->table('berita')
                ->where('status', 'publish')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $beritaTerbaru = [];
        }

        try {
            $pesanTerbaru = $db->table('kontak')
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $pesanTerbaru = [];
        }

        try {
            $periodStmt = $db->getConnection()->query(
                "SELECT
                    cvd.view_date,
                    SUM(CASE WHEN cvd.content_type = 'berita' THEN cvd.view_count ELSE 0 END) AS berita_views,
                    SUM(CASE WHEN cvd.content_type = 'galeri' THEN cvd.view_count ELSE 0 END) AS galeri_views,
                    SUM(cvd.view_count) AS total_views
                 FROM content_views_daily cvd
                 WHERE cvd.view_date >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
                 GROUP BY cvd.view_date
                 ORDER BY cvd.view_date DESC"
            );
            $periodViews = $periodStmt ? ($periodStmt->fetchAll() ?: []) : [];
        } catch (\Throwable $e) {
            $periodViews = [];
        }

        try {
            $topStmt = $db->getConnection()->query(
                "SELECT
                    cvd.content_type,
                    cvd.content_id,
                    SUM(cvd.view_count) AS total_views,
                    CASE
                        WHEN cvd.content_type = 'berita' THEN (SELECT b.judul FROM berita b WHERE b.id = cvd.content_id LIMIT 1)
                        WHEN cvd.content_type = 'galeri' THEN (SELECT g.judul FROM galeri g WHERE g.id = cvd.content_id LIMIT 1)
                        ELSE '-'
                    END AS judul
                 FROM content_views_daily cvd
                 WHERE cvd.view_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 GROUP BY cvd.content_type, cvd.content_id
                 ORDER BY total_views DESC
                 LIMIT 10"
            );
            $topContentViews = $topStmt ? ($topStmt->fetchAll() ?: []) : [];
        } catch (\Throwable $e) {
            $topContentViews = [];
        }

        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';
        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';

        $data = [
            'title' => 'Dashboard Admin - ' . $websiteNama,
            'page' => 'admin-dashboard',
            'website_nama' => $websiteNama,
            'website_deskripsi' => $pengaturan['website_deskripsi'] ?? '',
            'desa_nama' => $desaNama,
            'user_name' => Session::get('user_name', 'Administrator'),
            'user_role' => Session::get('user_role', 'admin'),
            'metrics' => $metrics,
            'berita_terbaru' => $beritaTerbaru,
            'pesan_terbaru' => $pesanTerbaru,
            'period_views' => $periodViews,
            'top_content_views' => $topContentViews,
        ];

        return Response::make(View::renderWithLayout('pages/admin/dashboard', $data, 'layouts/admin'));
    }
}
