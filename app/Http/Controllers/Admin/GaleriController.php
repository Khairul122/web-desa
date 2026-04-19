<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class GaleriController extends Controller
{
    public function index(): Response
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];

        $kategoriFilter = trim((string) $this->request->get('kategori', ''));
        $mediaFilter = trim((string) $this->request->get('media', ''));
        $search = trim((string) $this->request->get('q', ''));
        $perPage = max(6, min(48, (int) $this->request->get('per_page', 12)));
        $page = max(1, (int) $this->request->get('page', 1));

        $allowedMedia = ['', 'image', 'video'];
        if (!in_array($mediaFilter, $allowedMedia, true)) {
            $mediaFilter = '';
        }

        $pdo = Database::getInstance()->getConnection();
        [$whereSql, $bindings] = $this->buildFilterWhere($kategoriFilter, $mediaFilter, $search);

        $countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM galeri g {$whereSql}");
        foreach ($bindings as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) ($countStmt->fetch()['total'] ?? 0);

        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $listStmt = $pdo->prepare(
            "SELECT g.id, g.judul, g.deskripsi, g.gambar, g.kategori, g.views, g.created_at
             FROM galeri g
             {$whereSql}
             ORDER BY g.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        foreach ($bindings as $key => $value) {
            $listStmt->bindValue($key, $value);
        }
        $listStmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $listStmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $listStmt->execute();

        $rows = $listStmt->fetchAll() ?: [];
        $galeri = array_map(fn($row) => $this->formatGaleriRow($row), $rows);

        $kategoriOptions = [];
        $kategoriStmt = $pdo->query("SELECT DISTINCT kategori FROM galeri WHERE kategori IS NOT NULL AND kategori <> '' ORDER BY kategori ASC");
        if ($kategoriStmt !== false) {
            $kategoriRows = $kategoriStmt->fetchAll() ?: [];
            foreach ($kategoriRows as $row) {
                $kategori = trim((string) ($row['kategori'] ?? ''));
                if ($kategori !== '') {
                    $kategoriOptions[] = $kategori;
                }
            }
        }

        $metricTotal = (int) (Database::getInstance()->table('galeri')->count());
        $metricImage = (int) ($pdo->query("SELECT COUNT(*) AS total FROM galeri WHERE " . $this->sqlMediaImageFilter())->fetch()['total'] ?? 0);
        $metricVideo = (int) ($pdo->query("SELECT COUNT(*) AS total FROM galeri WHERE " . $this->sqlMediaVideoFilter())->fetch()['total'] ?? 0);
        $metricViews = (int) ($pdo->query("SELECT COALESCE(SUM(views), 0) AS total FROM galeri")->fetch()['total'] ?? 0);

        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';
        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';

        $baseParams = [
            'kategori' => $kategoriFilter,
            'media' => $mediaFilter,
            'q' => $search,
            'per_page' => $perPage,
        ];

        $data = [
            'title' => 'Manajemen Galeri - ' . $websiteNama,
            'page' => 'admin-galeri',
            'website_nama' => $websiteNama,
            'website_deskripsi' => $pengaturan['website_deskripsi'] ?? '',
            'desa_nama' => $desaNama,
            'user_name' => Session::get('user_name', 'Administrator'),
            'user_role' => Session::get('user_role', 'admin'),
            'galeri' => $galeri,
            'kategori_options' => $kategoriOptions,
            'filters' => [
                'kategori' => $kategoriFilter,
                'media' => $mediaFilter,
                'q' => $search,
                'per_page' => $perPage,
            ],
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total),
            ],
            'base_params' => $baseParams,
            'metrics' => [
                'total' => $metricTotal,
                'image' => $metricImage,
                'video' => $metricVideo,
                'views' => $metricViews,
            ],
        ];

        return Response::make(View::renderWithLayout('pages/admin/galeri/index', $data, 'layouts/admin'));
    }

    public function store(): Response
    {
        $judul = trim((string) $this->request->post('judul', ''));
        $kategori = trim((string) $this->request->post('kategori', ''));
        $deskripsi = trim((string) $this->request->post('deskripsi', ''));
        $videoUrl = trim((string) $this->request->post('video_url', ''));

        if ($judul === '') {
            Session::flash('error', 'Judul galeri wajib diisi.');
            return $this->redirect(base_url('/admin/galeri'));
        }

        $mediaSource = $this->processIncomingMediaSource($videoUrl, true);
        if ($mediaSource === null) {
            Session::flash('error', 'Silakan upload file media atau isi URL video.');
            return $this->redirect(base_url('/admin/galeri'));
        }

        Database::getInstance()->table('galeri')->insert([
            'judul' => $judul,
            'deskripsi' => $deskripsi,
            'gambar' => $mediaSource,
            'kategori' => $kategori === '' ? null : $kategori,
            'views' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Media galeri berhasil ditambahkan.');
        return $this->redirect(base_url('/admin/galeri'));
    }

    public function edit(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('galeri')->where('id', $id)->first();

        if (!$item) {
            Session::flash('error', 'Data media tidak ditemukan.');
            return $this->redirect(base_url('/admin/galeri'));
        }

        return $this->galeriForm('edit', $this->formatGaleriRow($item));
    }

    public function update(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('galeri')->where('id', $id)->first();

        if (!$item) {
            Session::flash('error', 'Data media tidak ditemukan.');
            return $this->redirect(base_url('/admin/galeri'));
        }

        $judul = trim((string) $this->request->post('judul', ''));
        $kategori = trim((string) $this->request->post('kategori', ''));
        $deskripsi = trim((string) $this->request->post('deskripsi', ''));
        $videoUrl = trim((string) $this->request->post('video_url', ''));

        if ($judul === '') {
            Session::flash('error', 'Judul galeri wajib diisi.');
            return $this->redirect(base_url('/admin/galeri/edit/' . $id));
        }

        $newSource = $this->processIncomingMediaSource($videoUrl, false);
        $source = (string) ($item['gambar'] ?? '');

        if ($newSource !== null && $newSource !== '') {
            if ($source !== '' && !preg_match('#^https?://#i', $source)) {
                $oldPath = public_path('uploads/galeri/' . $source);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $source = $newSource;
        }

        Database::getInstance()->table('galeri')->where('id', $id)->update([
            'judul' => $judul,
            'kategori' => $kategori === '' ? null : $kategori,
            'deskripsi' => $deskripsi,
            'gambar' => $source,
        ]);

        Session::flash('success', 'Media galeri berhasil diperbarui.');
        return $this->redirect(base_url('/admin/galeri'));
    }

    public function destroy(): Response
    {
        $id = $this->resolveRouteId();
        if ($id <= 0) {
            Session::flash('error', 'ID media tidak valid.');
            return $this->redirect(base_url('/admin/galeri'));
        }

        $item = Database::getInstance()->table('galeri')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Data media tidak ditemukan.');
            return $this->redirect(base_url('/admin/galeri'));
        }

        $source = (string) ($item['gambar'] ?? '');
        if ($source !== '' && !preg_match('#^https?://#i', $source)) {
            $filePath = public_path('uploads/galeri/' . $source);
            if (is_file($filePath)) {
                @unlink($filePath);
            }
        }

        Database::getInstance()->table('galeri')->where('id', $id)->delete();
        Session::flash('success', 'Media galeri berhasil dihapus.');

        return $this->redirect(base_url('/admin/galeri'));
    }

    private function buildFilterWhere(string $kategoriFilter, string $mediaFilter, string $search): array
    {
        $whereParts = [];
        $bindings = [];

        if ($kategoriFilter !== '') {
            $whereParts[] = 'g.kategori = :kategori';
            $bindings[':kategori'] = $kategoriFilter;
        }

        if ($mediaFilter === 'image') {
            $whereParts[] = $this->sqlMediaImageFilter('g.gambar');
        } elseif ($mediaFilter === 'video') {
            $whereParts[] = $this->sqlMediaVideoFilter('g.gambar');
        }

        if ($search !== '') {
            $whereParts[] = '(g.judul LIKE :search OR g.deskripsi LIKE :search OR g.kategori LIKE :search OR g.gambar LIKE :search)';
            $bindings[':search'] = '%' . $search . '%';
        }

        if (empty($whereParts)) {
            return ['', $bindings];
        }

        return ['WHERE ' . implode(' AND ', $whereParts), $bindings];
    }

    private function formatGaleriRow(array $row): array
    {
        $source = (string) ($row['gambar'] ?? '');
        $isExternal = (bool) preg_match('#^https?://#i', $source);

        $mediaType = 'image';
        if ($this->isVideoSource($source)) {
            $mediaType = 'video';
        }

        $row['media_type'] = $mediaType;
        $row['media_url'] = $isExternal ? $source : upload_url('galeri/' . $source);
        $row['is_external'] = $isExternal;

        return $row;
    }

    private function isVideoSource(string $source): bool
    {
        $lower = strtolower($source);
        return preg_match('/\.(mp4|webm|ogg|mov|mkv)(\?.*)?$/', $lower) === 1
            || str_contains($lower, 'youtube.com')
            || str_contains($lower, 'youtu.be')
            || str_contains($lower, 'vimeo.com');
    }

    private function sqlMediaImageFilter(string $column = 'gambar'): string
    {
        return "(LOWER({$column}) LIKE '%.jpg%' OR LOWER({$column}) LIKE '%.jpeg%' OR LOWER({$column}) LIKE '%.png%' OR LOWER({$column}) LIKE '%.webp%' OR LOWER({$column}) LIKE '%.gif%')";
    }

    private function sqlMediaVideoFilter(string $column = 'gambar'): string
    {
        return "(LOWER({$column}) LIKE '%.mp4%' OR LOWER({$column}) LIKE '%.webm%' OR LOWER({$column}) LIKE '%.ogg%' OR LOWER({$column}) LIKE '%.mov%' OR LOWER({$column}) LIKE '%.mkv%' OR LOWER({$column}) LIKE '%youtube.com%' OR LOWER({$column}) LIKE '%youtu.be%' OR LOWER({$column}) LIKE '%vimeo.com%')";
    }

    private function galeriForm(string $mode, array $item): Response
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];
        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';
        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';

        $data = [
            'title' => ($mode === 'edit' ? 'Edit Media Galeri' : 'Galeri') . ' - ' . $websiteNama,
            'page' => 'admin-galeri',
            'website_nama' => $websiteNama,
            'desa_nama' => $desaNama,
            'user_name' => Session::get('user_name', 'Administrator'),
            'user_role' => Session::get('user_role', 'admin'),
            'mode' => $mode,
            'galeri_item' => $item,
        ];

        return Response::make(View::renderWithLayout('pages/admin/galeri/form', $data, 'layouts/admin'));
    }

    private function processIncomingMediaSource(string $videoUrl, bool $required): ?string
    {
        if ($this->request->hasFile('media_file')) {
            $file = $this->request->file('media_file');
            if (!is_array($file)) {
                Session::flash('error', 'Data upload media tidak valid.');
                return null;
            }

            $uploadErrorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
            if ($uploadErrorCode !== UPLOAD_ERR_OK) {
                if ($uploadErrorCode !== UPLOAD_ERR_NO_FILE) {
                    Session::flash('error', 'Upload media gagal: ' . $this->uploadErrorMessage($uploadErrorCode));
                } else {
                    Session::flash('error', 'Upload media gagal. Pastikan file valid.');
                }
                return null;
            }

            $tmpPath = (string) ($file['tmp_name'] ?? '');
            $originalName = (string) ($file['name'] ?? 'media');
            $fileSize = (int) ($file['size'] ?? 0);
            if ($fileSize <= 0 || $fileSize > 20 * 1024 * 1024) {
                Session::flash('error', 'Ukuran media maksimal 20MB.');
                return null;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = $finfo ? (string) finfo_file($finfo, $tmpPath) : '';
            if ($finfo) {
                finfo_close($finfo);
            }

            $imageMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $videoMime = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-matroska'];

            if (!in_array($mimeType, array_merge($imageMime, $videoMime), true)) {
                Session::flash('error', 'Format media tidak didukung. Gunakan gambar atau video umum.');
                return null;
            }

            $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
            if ($extension === '') {
                $extension = in_array($mimeType, $videoMime, true) ? 'mp4' : 'jpg';
            }

            $isVideo = in_array($mimeType, $videoMime, true);
            $prefix = $isVideo ? 'galeri_vid_' : 'galeri_img_';
            $randomPart = substr(md5((string) microtime(true)), 0, 6);
            $fileName = $prefix . time() . '_' . $randomPart . '.' . $extension;
            $uploadDir = public_path('uploads/galeri');
            if (!$this->ensureUploadDirectory($uploadDir)) {
                Session::flash('error', 'Direktori upload galeri tidak dapat dibuat.');
                return null;
            }

            $destination = public_path('uploads/galeri/' . $fileName);
            if (!$this->moveUploadedFileSafely($tmpPath, $destination)) {
                Session::flash('error', 'Gagal menyimpan file media.');
                return null;
            }

            return $fileName;
        }

        if ($videoUrl !== '') {
            if (!filter_var($videoUrl, FILTER_VALIDATE_URL)) {
                Session::flash('error', 'URL video tidak valid.');
                return null;
            }
            return $videoUrl;
        }

        return $required ? null : '';
    }

    private function resolveRouteId(): int
    {
        $id = (int) $this->request->getParam('id', 0);
        if ($id > 0) {
            return $id;
        }

        $uri = (string) $this->request->getUri();
        if (preg_match('#/(\d+)$#', $uri, $matches) === 1) {
            return (int) $matches[1];
        }

        return 0;
    }
}
