<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class BeritaController extends Controller
{
    public function index(): Response
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];

        $statusFilter = trim((string) $this->request->get('status', ''));
        $kategoriFilter = trim((string) $this->request->get('kategori', ''));
        $search = trim((string) $this->request->get('q', ''));
        $perPage = max(5, min(50, (int) $this->request->get('per_page', 10)));
        $page = max(1, (int) $this->request->get('page', 1));

        $allowedStatus = ['publish', 'draft'];
        if (!in_array($statusFilter, $allowedStatus, true)) {
            $statusFilter = '';
        }

        $db = Database::getInstance()->getConnection();

        $whereParts = [];
        $bindings = [];

        if ($statusFilter !== '') {
            $whereParts[] = 'b.status = :status';
            $bindings[':status'] = $statusFilter;
        }

        if ($kategoriFilter !== '') {
            $whereParts[] = 'b.kategori = :kategori';
            $bindings[':kategori'] = $kategoriFilter;
        }

        if ($search !== '') {
            $whereParts[] = '(b.judul LIKE :search OR b.slug LIKE :search OR b.konten LIKE :search)';
            $bindings[':search'] = '%' . $search . '%';
        }

        $whereSql = '';
        if (!empty($whereParts)) {
            $whereSql = 'WHERE ' . implode(' AND ', $whereParts);
        }

        $countSql = "SELECT COUNT(*) AS total FROM berita b {$whereSql}";
        $countStmt = $db->prepare($countSql);
        foreach ($bindings as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int) ($countStmt->fetch()['total'] ?? 0);

        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);
        $offset = ($page - 1) * $perPage;

        $listSql = "
            SELECT b.id, b.judul, b.slug, b.kategori, b.status, b.views, b.created_at,
                   COALESCE(u.nama_lengkap, '-') AS penulis
            FROM berita b
            LEFT JOIN users u ON u.id = b.penulis_id
            {$whereSql}
            ORDER BY b.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $listStmt = $db->prepare($listSql);
        foreach ($bindings as $key => $value) {
            $listStmt->bindValue($key, $value);
        }
        $listStmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $listStmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $listStmt->execute();
        $berita = $listStmt->fetchAll() ?: [];

        $kategoriRows = [];
        $kategoriStmt = $db->query("SELECT DISTINCT kategori FROM berita WHERE kategori IS NOT NULL AND kategori <> '' ORDER BY kategori ASC");
        if ($kategoriStmt !== false) {
            $kategoriRows = $kategoriStmt->fetchAll() ?: [];
        }

        $kategoriOptions = [];
        foreach ($kategoriRows as $row) {
            $kategori = trim((string) ($row['kategori'] ?? ''));
            if ($kategori !== '') {
                $kategoriOptions[$kategori] = $kategori;
            }
        }

        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';
        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';

        $baseParams = [
            'status' => $statusFilter,
            'kategori' => $kategoriFilter,
            'q' => $search,
            'per_page' => $perPage,
        ];

        $data = [
            'title' => 'Manajemen Berita - ' . $websiteNama,
            'page' => 'admin-berita',
            'website_nama' => $websiteNama,
            'website_deskripsi' => $pengaturan['website_deskripsi'] ?? '',
            'desa_nama' => $desaNama,
            'user_name' => Session::get('user_name', 'Administrator'),
            'user_role' => Session::get('user_role', 'admin'),
            'berita' => $berita,
            'kategori_options' => array_values($kategoriOptions),
            'filters' => [
                'status' => $statusFilter,
                'kategori' => $kategoriFilter,
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
        ];

        return Response::make(View::renderWithLayout('pages/admin/berita/index', $data, 'layouts/admin'));
    }

    public function create(): Response
    {
        return $this->beritaForm('create', []);
    }

    public function store(): Response
    {
        $judul = trim((string) $this->request->post('judul', ''));
        $kategori = trim((string) $this->request->post('kategori', ''));
        $status = trim((string) $this->request->post('status', 'draft'));
        $konten = $this->normalizeEditorHtml((string) $this->request->post('konten', ''));

        if ($judul === '' || $konten === '') {
            Session::flash('error', 'Judul dan konten wajib diisi.');
            return $this->redirect(base_url('/admin/berita/create'));
        }

        if (!in_array($status, ['publish', 'draft'], true)) {
            $status = 'draft';
        }

        $slug = slug($judul);
        $slug = $this->ensureUniqueSlug($slug);
        $thumbnail = $this->handleThumbnailUpload();

        Database::getInstance()->table('berita')->insert([
            'judul' => $judul,
            'slug' => $slug,
            'konten' => $konten,
            'thumbnail' => $thumbnail,
            'penulis_id' => (int) Session::get('user_id', 0) ?: null,
            'kategori' => $kategori === '' ? null : $kategori,
            'status' => $status,
            'views' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Berita berhasil ditambahkan.');
        return $this->redirect(base_url('/admin/berita'));
    }

    public function edit(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('berita')->where('id', $id)->first();

        if (!$item) {
            Session::flash('error', 'Berita tidak ditemukan.');
            return $this->redirect(base_url('/admin/berita'));
        }

        return $this->beritaForm('edit', $item);
    }

    public function update(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('berita')->where('id', $id)->first();

        if (!$item) {
            Session::flash('error', 'Berita tidak ditemukan.');
            return $this->redirect(base_url('/admin/berita'));
        }

        $judul = trim((string) $this->request->post('judul', ''));
        $kategori = trim((string) $this->request->post('kategori', ''));
        $status = trim((string) $this->request->post('status', 'draft'));
        $konten = $this->normalizeEditorHtml((string) $this->request->post('konten', ''));

        if ($judul === '' || $konten === '') {
            Session::flash('error', 'Judul dan konten wajib diisi.');
            return $this->redirect(base_url('/admin/berita/edit/' . $id));
        }

        if (!in_array($status, ['publish', 'draft'], true)) {
            $status = 'draft';
        }

        $slug = slug($judul);
        $slug = $this->ensureUniqueSlug($slug, $id);

        $thumbnail = (string) ($item['thumbnail'] ?? '');
        $uploadedThumbnail = $this->handleThumbnailUpload();
        if ($uploadedThumbnail !== null) {
            if ($thumbnail !== '') {
                $old = public_path('uploads/artikel/' . $thumbnail);
                if (is_file($old)) {
                    @unlink($old);
                }
            }
            $thumbnail = $uploadedThumbnail;
        }

        Database::getInstance()->table('berita')->where('id', $id)->update([
            'judul' => $judul,
            'slug' => $slug,
            'konten' => $konten,
            'thumbnail' => $thumbnail,
            'kategori' => $kategori === '' ? null : $kategori,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Berita berhasil diperbarui.');
        return $this->redirect(base_url('/admin/berita'));
    }

    public function destroy(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('berita')->where('id', $id)->first();

        if (!$item) {
            Session::flash('error', 'Berita tidak ditemukan.');
            return $this->redirect(base_url('/admin/berita'));
        }

        $thumbnail = (string) ($item['thumbnail'] ?? '');
        if ($thumbnail !== '') {
            $path = public_path('uploads/artikel/' . $thumbnail);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        Database::getInstance()->table('berita')->where('id', $id)->delete();
        Session::flash('success', 'Berita berhasil dihapus.');

        return $this->redirect(base_url('/admin/berita'));
    }

    private function beritaForm(string $mode, array $item): Response
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];
        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';
        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';

        $data = [
            'title' => ($mode === 'edit' ? 'Edit Berita' : 'Tambah Berita') . ' - ' . $websiteNama,
            'page' => 'admin-berita',
            'website_nama' => $websiteNama,
            'desa_nama' => $desaNama,
            'user_name' => Session::get('user_name', 'Administrator'),
            'user_role' => Session::get('user_role', 'admin'),
            'mode' => $mode,
            'berita_item' => $item,
        ];

        return Response::make(View::renderWithLayout('pages/admin/berita/form', $data, 'layouts/admin'));
    }

    private function ensureUniqueSlug(string $baseSlug, int $ignoreId = 0): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'berita';
        $candidate = $slug;
        $counter = 1;

        while (true) {
            $query = Database::getInstance()->table('berita')->where('slug', $candidate);
            $existing = $query->first();
            if (!$existing || (int) ($existing['id'] ?? 0) === $ignoreId) {
                return $candidate;
            }
            $counter++;
            $candidate = $slug . '-' . $counter;
        }
    }

    private function handleThumbnailUpload(): ?string
    {
        if (!$this->request->hasFile('thumbnail')) {
            return null;
        }

        $file = $this->request->file('thumbnail');
        if (!is_array($file)) {
            Session::flash('error', 'Data upload thumbnail tidak valid.');
            return null;
        }

        $uploadErrorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadErrorCode !== UPLOAD_ERR_OK) {
            if ($uploadErrorCode !== UPLOAD_ERR_NO_FILE) {
                Session::flash('error', 'Upload thumbnail gagal: ' . $this->uploadErrorMessage($uploadErrorCode));
            }
            return null;
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $originalName = (string) ($file['name'] ?? 'thumb');
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > 5 * 1024 * 1024) {
            Session::flash('error', 'Ukuran thumbnail maksimal 5MB.');
            return null;
        }

        if (@getimagesize($tmpPath) === false) {
            Session::flash('error', 'File thumbnail bukan gambar valid.');
            return null;
        }

        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return null;
        }

        $uploadDir = public_path('uploads/artikel');
        if (!$this->ensureUploadDirectory($uploadDir)) {
            Session::flash('error', 'Folder upload artikel tidak tersedia atau tidak bisa ditulis.');
            return null;
        }

        $fileName = 'berita_' . time() . '_' . substr(md5((string) microtime(true)), 0, 6) . '.' . $extension;
        $destination = public_path('uploads/artikel/' . $fileName);

        if (!$this->moveUploadedFileSafely($tmpPath, $destination)) {
            Session::flash('error', 'Server gagal menyimpan thumbnail ke folder uploads/artikel.');
            return null;
        }

        return $fileName;
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
