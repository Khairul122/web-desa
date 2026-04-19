<?php

namespace App\Http\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Models\ProfilDesa;

class CarouselController extends Controller
{
    public function index(): Response
    {
        $items = Database::getInstance()->table('carousel')->orderBy('urutan', 'ASC')->orderBy('id', 'DESC')->get() ?: [];

        return Response::make(View::renderWithLayout('pages/admin/carousel/index', $this->baseData([
            'title' => 'Manajemen Carousel',
            'page' => 'admin-carousel',
            'carousel' => $items,
        ]), 'layouts/admin'));
    }

    public function create(): Response
    {
        return Response::make(View::renderWithLayout('pages/admin/carousel/form', $this->baseData([
            'title' => 'Tambah Slide Carousel',
            'page' => 'admin-carousel',
            'mode' => 'create',
            'carousel_item' => [],
        ]), 'layouts/admin'));
    }

    public function store(): Response
    {
        $judul = trim((string) $this->request->post('judul', ''));
        $deskripsi = trim((string) $this->request->post('deskripsi', ''));
        $urutan = (int) $this->request->post('urutan', 0);
        $isActive = $this->request->post('is_active') ? 1 : 0;

        $gambar = $this->handleImageUpload(true);
        if ($gambar === null) {
            Session::flash('error', 'Gambar wajib diupload dan harus berupa JPG/PNG/WEBP/GIF.');
            return $this->redirect(base_url('/admin/carousel/create'));
        }

        Database::getInstance()->table('carousel')->insert([
            'judul' => $judul === '' ? null : $judul,
            'deskripsi' => $deskripsi === '' ? null : $deskripsi,
            'gambar' => $gambar,
            'urutan' => $urutan,
            'is_active' => $isActive,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Slide carousel berhasil ditambahkan.');
        return $this->redirect(base_url('/admin/carousel'));
    }

    public function edit(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('carousel')->where('id', $id)->first();

        if (!$item) {
            Session::flash('error', 'Data slide tidak ditemukan.');
            return $this->redirect(base_url('/admin/carousel'));
        }

        return Response::make(View::renderWithLayout('pages/admin/carousel/form', $this->baseData([
            'title' => 'Edit Slide Carousel',
            'page' => 'admin-carousel',
            'mode' => 'edit',
            'carousel_item' => $item,
        ]), 'layouts/admin'));
    }

    public function update(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('carousel')->where('id', $id)->first();
        if (!$item) {
            Session::flash('error', 'Data slide tidak ditemukan.');
            return $this->redirect(base_url('/admin/carousel'));
        }

        $judul = trim((string) $this->request->post('judul', ''));
        $deskripsi = trim((string) $this->request->post('deskripsi', ''));
        $urutan = (int) $this->request->post('urutan', 0);
        $isActive = $this->request->post('is_active') ? 1 : 0;

        $newImage = $this->handleImageUpload(false);
        $gambar = (string) ($item['gambar'] ?? '');
        if ($newImage !== null) {
            if ($gambar !== '') {
                $oldPath = public_path('uploads/carousel/' . $gambar);
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $gambar = $newImage;
        }

        Database::getInstance()->table('carousel')->where('id', $id)->update([
            'judul' => $judul === '' ? null : $judul,
            'deskripsi' => $deskripsi === '' ? null : $deskripsi,
            'gambar' => $gambar,
            'urutan' => $urutan,
            'is_active' => $isActive,
        ]);

        Session::flash('success', 'Slide carousel berhasil diperbarui.');
        return $this->redirect(base_url('/admin/carousel'));
    }

    public function destroy(): Response
    {
        $id = $this->resolveRouteId();
        $item = Database::getInstance()->table('carousel')->where('id', $id)->first();

        if (!$item) {
            Session::flash('error', 'Data slide tidak ditemukan.');
            return $this->redirect(base_url('/admin/carousel'));
        }

        $gambar = (string) ($item['gambar'] ?? '');
        if ($gambar !== '') {
            $path = public_path('uploads/carousel/' . $gambar);
            if (is_file($path)) {
                @unlink($path);
            }
        }

        Database::getInstance()->table('carousel')->where('id', $id)->delete();
        Session::flash('success', 'Slide carousel berhasil dihapus.');
        return $this->redirect(base_url('/admin/carousel'));
    }

    private function handleImageUpload(bool $required): ?string
    {
        if (!$this->request->hasFile('gambar')) {
            return $required ? null : null;
        }

        $file = $this->request->file('gambar');
        if (!is_array($file)) {
            Session::flash('error', 'Data upload gambar tidak valid.');
            return null;
        }

        $uploadErrorCode = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($uploadErrorCode !== UPLOAD_ERR_OK) {
            if ($uploadErrorCode !== UPLOAD_ERR_NO_FILE) {
                Session::flash('error', 'Upload gambar gagal: ' . $this->uploadErrorMessage($uploadErrorCode));
            }
            return null;
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        $originalName = (string) ($file['name'] ?? 'carousel');
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > 5 * 1024 * 1024) {
            Session::flash('error', 'Ukuran gambar maksimal 5MB.');
            return null;
        }

        if (@getimagesize($tmpPath) === false) {
            Session::flash('error', 'File gambar bukan gambar valid.');
            return null;
        }

        $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return null;
        }

        $dir = public_path('uploads/carousel');
        if (!$this->ensureUploadDirectory($dir)) {
            Session::flash('error', 'Folder upload carousel tidak tersedia atau tidak bisa ditulis.');
            return null;
        }

        $fileName = 'carousel_' . time() . '_' . substr(md5((string) microtime(true)), 0, 6) . '.' . $ext;
        $destination = public_path('uploads/carousel/' . $fileName);
        if (!$this->moveUploadedFileSafely($tmpPath, $destination)) {
            Session::flash('error', 'Server gagal menyimpan gambar ke folder uploads/carousel.');
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

    private function baseData(array $extra = []): array
    {
        $profil = ProfilDesa::first() ?: [];
        $pengaturan = Pengaturan::getAll() ?: [];
        $websiteNama = trim((string) ($pengaturan['website_nama'] ?? '')) ?: 'Website Desa';
        $desaNama = trim((string) ($profil['nama_desa'] ?? '')) ?: 'Desa';

        return array_merge([
            'website_nama' => $websiteNama,
            'website_deskripsi' => (string) ($pengaturan['website_deskripsi'] ?? ''),
            'desa_nama' => $desaNama,
            'user_name' => Session::get('user_name', 'Administrator'),
            'user_role' => Session::get('user_role', 'admin'),
        ], $extra);
    }
}
